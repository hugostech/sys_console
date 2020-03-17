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
        foreach (Ex_product::where('status', 1)->has('description')->cursor() as $ex_product){

            $data[] = $this->transformer($ex_product);

        };
        file_put_contents('/image/feed.json',\GuzzleHttp\json_encode($data));

    }

    private function transformer(Ex_product $product){
        $price = $product->special?$product->special->price:$product->price;
        return [
            'Product ID' => $product->product_id,
            'Category' => $product->categorys()->first()?$product->categorys()->first()->description->name:'more',
            'Brand' => $product->brand?$product->brand->name:'',
            'Product name' => $product->description->name,
            'URL' => 'https://www.extremepc.co.nz/index.php?route=product/product&product_id='.$product->product_id,
            'Price' => round($price*1.15,2),
            'Condition' => 'New',
            'MPN' => $product->mpn,
            'Shipping' => 5,
            'Stock status' => $product->quantity > 0?'In stock':'Out of stock',
            'Availability' => $product->quantity > 0?'available':'Can not be ordered',
        ];
    }
}
