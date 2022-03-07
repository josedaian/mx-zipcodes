<?php

namespace App\Console\Commands;

use App\Models\LogZipCode;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Console\Command;
use Throwable;

class PopulateZipCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zip-code:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate table from a xlsx resource';

    public function handle()
    {
        $initialZipCodes = LogZipCode::pluck('zipcode')->toArray();
        $newZipCodes = [];
        $pathToXls = public_path('zipcodes-dummy.xlsx');
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($pathToXls);

        foreach ($reader->getSheetIterator() as $sheet) {
            if($sheet->getIndex() === 0){
                continue; //Omitimos la primera pestaña del excel
            }

            foreach ($sheet->getRowIterator() as $rowKey => $row) {
                if(0 === $rowKey){
                    continue;
                }

                foreach ($row->getCells() as $cellKey => $cell){
                    if(0 === $cellKey){
                        if(!in_array($cell, $initialZipCodes)){
                            $this->info(sprintf('Nuevo zipCode %s para popular la tabla', $cell));
                            $newZipCodes[] = ['zipcode' => $cell];
                            $initialZipCodes[] = $cell;
                        }
                        break;
                    }
                }
            }

            if(!empty($newZipCodes)){
                try {
                    LogZipCode::upsert($newZipCodes, ['zipcode']);
                    $this->info(sprintf('%s nuevos zipCodes insertados', count($newZipCodes)));
                    $this->warn('Vaciando newZipcodes...');
                    $newZipCodes = [];
                }catch (Throwable $th){
                    $this->error($th->getMessage());
                    break;
                }
            }
        }

        $reader->close();
    }
}
