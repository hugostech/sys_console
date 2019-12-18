<?php

namespace App\Console\Commands;

use App\Ex_product;
use Illuminate\Console\Command;

class GenerateProductFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate product feed for pricespy and priceme';

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
        $data = [];
        foreach (Ex_product::where('status', 1)->has('description')->has('stock')->cursor() as $ex_product){

            $data[] = $this->transformer($ex_product);

        };
        file_put_contents(storage_path('csv/extremepc.json'),\GuzzleHttp\json_encode($data));

    }

    private function transformer(Ex_product $product){
        return [
            'Product ID' => $product->product_id,
            'Category' => $product->categorys()->first()?$product->categorys()->first()->description->name:'more',
            'Brand' => $product->brand?$product->brand->name:'',
            'Product name' => $product->description->name,
            'URL' => 'https://www.extremepc.co.nz/index.php?route=product/product&product_id='.$product->product_id,
            'Price' => $product->special?$product->special->price:$product->price,
            'Condition' => 'New',
            'MPN' => $product->mpn,
            'Shipping' => 5,
            'Stock status' => ($product->quantity > 0 || $product->stock->supplier>0)?'In stock':'Out of stock',
            'Availability' => ($product->quantity > 0 || $product->stock->supplier>0)?'available':'Can not be ordered',
        ];
    }
}
