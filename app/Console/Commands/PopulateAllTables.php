<?php

namespace App\Console\Commands;

use App\Models\FederalEntity;
use App\Models\LogZipCode;
use App\Models\Municipality;
use App\Models\Settlement;
use App\Models\SettlementType;
use App\Models\ZipCode;
use App\Traits\ApiRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PopulateAllTables extends Command
{
    use ApiRequest;
    private $mainUrl = 'https://staging.boolean.mx/api';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zip-code:populate';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate all related tables';

    public function handle()
    {
        $zipCodes = LogZipCode::whereNotIn('zipcode', function($query){
            $query->select('zip_code')
                ->from('zip_codes');
        })->pluck('zipcode')->toArray();

        $pendingToProcess = count($zipCodes);
        $processed = 0;
        foreach ($zipCodes as $zipCode){
            $response = $this->apiGet($this->getEndpointWithRequestOptions('/zip-codes/'.$zipCode));
            $responseObject = $response->object();
            $processed++;
            if(empty($responseObject) || isset($responseObject->result)){
                continue;
            }
            $this->moveOnDatabase($responseObject);
            $this->info(sprintf('Nuevo zipcode in the house. ZIPCODE:%s | Estado %d/%d', $responseObject->zip_code, $pendingToProcess, $processed));
        }
    }

    protected function getEndpointWithRequestOptions(string $endpoint, array $options = []): array
    {
        return [
            'url' => $this->mainUrl . $endpoint,
            'endpoint' => $endpoint,
            'headers' => [],
            'options' => [ // GuzzleHttp Options
                'connect_timeout' => 60,
                'verify' => false,
            ]
        ];
    }

    /**
     * @param object $responseObject
     * @return void
     */
    public function moveOnDatabase(object $responseObject): void
    {
        if(isset($responseObject->federal_entity)){
            $federalEntity = FederalEntity::updateOrCreate([
                'code' => $responseObject->federal_entity->code,
                'name' => $responseObject->federal_entity->name,
            ]);
        }

        if(isset($responseObject->municipality)){
            $municipality = Municipality::updateOrCreate([
                'slug' => Str::slug($responseObject->municipality->name),
                'name' => $responseObject->municipality->name,
            ]);
        }

        $zipCodeModel = ZipCode::updateOrCreate([
            'zip_code' => $responseObject->zip_code,
            'locality' => $responseObject->locality,
            'federal_entity_id' => isset($federalEntity) ? $federalEntity->id : null,
            'municipality_id' => isset($municipality) ? $municipality->id : null
        ]);

        foreach ($responseObject->settlements as $settlement) {
            $settlementType = SettlementType::updateOrCreate([
                'slug' => Str::slug($settlement->settlement_type->name),
                'name' => $settlement->settlement_type->name,
            ]);

            Settlement::upsert([
                'slug' => Str::slug($settlement->name),
                'name' => $settlement->name,
                'zone_type' => $settlement->zone_type,
                'settlement_type_id' => $settlementType->id,
                'zip_code_id' => $zipCodeModel->id
            ], ['slug']);
        }
    }
}
