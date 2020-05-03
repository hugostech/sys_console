<?php

namespace App\Console\Commands;

use App\Ex_product;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateGoogleFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:google';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate google feed';

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
        $fileName = '/image/extremepcFeed.xls';
        $query = Ex_product::where('status', 1)->where('quantity', '>', 0)->has('description');
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($fileName);
        $writer->addRow(['id','title','description','link','image_link','price','availability','brand','gtin', 'MPN','shipping']);
        try{
            foreach ( $query->cursor() as $ex_product){
                $row = WriterEntityFactory::createRowFromArray($this->transform($ex_product));
                $writer->addRow($row);
            };

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
        }
        $writer->close();

    }

    private function transform(Ex_product $product){
        $price = $product->special?$product->special->price:$product->price;
        return [
            'id' => $product->product_id,
            'title' => $product->description->name,
            'description' => $product->description->name,
            'link' => 'https://www.extremepc.co.nz/index.php?route=product/product&product_id='.$product->product_id,
            'image_link' => 'https://www.extremepc.co.nz/image/'.$product->image,
            'price' => round($price*1.15,2). ' NZD',
            'availability' => $product->quantity > 0?'in stock':'out of stock',
            'brand' => $product->brand?$product->brand->name:'',
            'gtin' => '',
            'MPN' => $product->mpn,
            'shipping' => 5,
        ];
    }
}
