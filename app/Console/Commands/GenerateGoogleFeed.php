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
    protected $signature = 'feed:google 
                            {--type=xml : feed type, xml or xls}';

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
        if ($this->option('type') == 'xls'){
            $fileName = '/image/extremepcFeed.xls';
            $query = Ex_product::where('status', 1)->where('quantity', '>', 0)->has('description');
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile($fileName);
            $writer->addRow(WriterEntityFactory::createRowFromArray(['id','title','description','link','image_link','price','availability','brand','gtin', 'MPN','shipping']));
            try{
                foreach ( $query->cursor() as $ex_product){
                    $row = WriterEntityFactory::createRowFromArray($this->transform($ex_product));
                    $writer->addRow($row);
                };

            }catch (\Exception $exception){
                Log::error($exception->getMessage());
            }
            $writer->close();
        }else{
            $fileName = '/image/extremepcFeed.xml';
            $xml_data = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
            $xml_data->addChild('title', 'ExtremePC');
            $xml_data->addChild('link', 'https://www.extremepc.co.nz');
            foreach (Ex_product::where('status', 1)->where('quantity', '>', 0)->has('description')->cursor() as $product){
                $node = $xml_data->addChild('item');
                $row = $this->transform($product);
                $row = array_flip($row);
                array_walk_recursive($row, array($node, 'addChild'));
            }
            $xml_data->asXML($fileName);
        }


    }

    private function transform(Ex_product $product){
        $price = $product->special?$product->special->price:$product->price;
        return [
            'id' => $product->product_id,
            'title' => htmlspecialchars($product->description->name),
            'description' => htmlspecialchars($product->description->name.' '.$product->categorys()->first()->description->name),
            'link' => htmlspecialchars('https://www.extremepc.co.nz/index.php?route=product/product&product_id='.$product->product_id),
            'g:image_link' => htmlspecialchars('https://www.extremepc.co.nz/image/'.$product->image),
            'g:price' => round($price*1.15,2). ' NZD',
            'g:availability' => $product->quantity > 0?'in stock':'out of stock',
            'brand' => htmlspecialchars($product->brand?$product->brand->name:''),
            'MPN' => htmlspecialchars($product->mpn),
            'shipping' => 5,
        ];
    }
}
