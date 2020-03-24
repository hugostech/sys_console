<?php

namespace App\Console\Commands;

use App\Ex_category;
use App\Ex_product;
use App\Ex_product_csv;
use backend\CSVReader;
use backend\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:read
                            {--readonly : output first 5 rows data from each csv file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'read csv from folder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        if (!$this->option('readonly')){
            ini_set('memory_limit', -1);
            // perpare csv import
            Ex_category::find(Product::MORECATORY)->products()->update(['status'=>0]);
            Ex_product::all()->update(['upc'=>0]);
            Ex_product_csv::truncate();

            foreach (glob(storage_path('csv').'/*.*') as $file){
                $csv = CSVReader::loadCSVByFile(last(explode('/', $file)));
                if ($csv){
                    $csv->process(false);
                }else{
                    Log::error("$file import fail");
                }

            }

            foreach (Ex_product::whereNotNull('mpn')->where('quantity', '<=', 0)->has('csvs')->cursor() as $ex_product){
                if ($ex_product->quantity<=0){
                    $data = [
                        'status' => 1,
                        'upc' => 1,
                    ];
                    if ($ex_product->price_lock==0){
                        $data['price']=$ex_product->csvs()->min('price_to_sell');
                    }
                    $ex_product->update($data);
                }
            };
        }else{
            foreach (glob(storage_path('csv').'/*.*') as $file){
                $csv = CSVReader::loadCSVByFile(last(explode('/', $file)));
                $this->info("Start Reading $file");
                if ($csv){
                    $data = $csv->read(10);
                    foreach ($data as $row){
                        $this->line(\GuzzleHttp\json_encode($row));
                    }
                $this->info('Read Finish');
                }else{
                    Log::error("$file read fail");
                }

            }
        }


    }
}
