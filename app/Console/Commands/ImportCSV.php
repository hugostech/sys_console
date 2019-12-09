<?php

namespace App\Console\Commands;

use App\Ex_category;
use App\Ex_product;
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
    protected $signature = 'csv:read';

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
        ini_set('memory_limit', -1);
        Ex_category::find(Product::MORECATORY)->products()->update(['status'=>0]);
        foreach (glob(storage_path('csv').'/*.*') as $file){
            $csv = CSVReader::loadCSVByFile(last(explode('/', $file)));
            if ($csv){
                $csv->process(false);
            }else{
                Log::error("$file import fail");
            }

        }

//        sleep(60*10);
        foreach (Ex_product::whereNotNull('mpn')->has('csvs')->has('stock')->cursor() as $ex_product){
            if ($ex_product->quantity<=0){
                $data = [
                    'status' => 1,
                ];
                if ($ex_product->price_lock==0){
                    $data['price']=$ex_product->csvs()->min('price_to_sell');
                }
                $ex_product->update($data);
            }
            $ex_product->stock()->update(['supplier'=>$ex_product->csvs()->sum('stock')]);
        };

    }
}
