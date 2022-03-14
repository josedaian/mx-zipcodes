<?php

namespace App\Console\Commands;

use App\Models\FederalEntity;
use App\Models\Municipality;
use App\Models\Settlement;
use App\Models\ZipCode;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Console\Command;
use Throwable;

class PopulateKeyForRelatedTables extends Command
{
    CONST ZIP_CODE_COLUMN_POSITION = 0;
    CONST SETTLEMENT_COLUMN_POSITION = 1;
    CONST MUNICIPALITY_COLUMN_POSITION = 3;
    CONST FEDERAL_ENTITY_COLUMN_POSITION = 4;
    CONST LOCALITY_COLUMN_POSITION = 5;
    CONST FEDELAR_ENTITY_KEY_COLUMN_POSITION = 7;
    CONST MUNICIPALITY_KEY_COLUMN_POSITION = 11;
    CONST SETTLEMENT_KEY_COLUMN_POSITION = 12;
    CONST SETTLEMENT_ZONE_TYPE_COLUMN_POSITION = 13;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zip-code:set-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate key column from a xlsx resource';

    public function handle()
    {
        $pathToXls = public_path('zipcodes-dummy.xlsx');
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($pathToXls);

        foreach ($reader->getSheetIterator() as $sheet) {
            if($sheet->getIndex() === 0){
                continue; //Omitimos la primera pestaña del excel
            }

            foreach ($sheet->getRowIterator() as $rowKey => $row) {
                if(1 === $rowKey){
                    continue; //omitimos la cabecera
                }

                $cells = $row->getCells();
                $municipality = Municipality::where('name', $this->normalizeToUTF8($cells[self::MUNICIPALITY_COLUMN_POSITION]))->first();
                if($municipality){
                    $this->info(sprintf('Actualizando municipalidad:%s', $municipality->name));
                    $municipality->key = $cells[self::MUNICIPALITY_KEY_COLUMN_POSITION] ?? null;
                    $municipality->save();
                }

                $federalEntity = FederalEntity::where('name', $this->normalizeToUTF8($cells[self::FEDERAL_ENTITY_COLUMN_POSITION]))->first();
                if($federalEntity){
                    $this->info(sprintf('Actualizando entidad federal:%s', $federalEntity->name));
                    $federalEntity->key = $cells[self::FEDELAR_ENTITY_KEY_COLUMN_POSITION] ?? null;
                    $federalEntity->save();
                }

                $settlement = Settlement::where('name', $this->normalizeToUTF8($cells[self::SETTLEMENT_COLUMN_POSITION]))
                    ->where('zone_type', $cells[self::SETTLEMENT_ZONE_TYPE_COLUMN_POSITION])
                    ->first();
                if($settlement){
                    $this->info(sprintf('Actualizando asentamiento:%s', $settlement->name));
                    $settlement->key = $cells[self::SETTLEMENT_KEY_COLUMN_POSITION] ?? null;
                    $settlement->save();
                }

                $this->newLine();
                $this->info('siguiente registro...');
            }
        }

        $reader->close();
    }

    protected function normalizeToUTF8(string $value): string{
        return iconv('UTF-8', 'ASCII//TRANSLIT', $value);
    }
}
