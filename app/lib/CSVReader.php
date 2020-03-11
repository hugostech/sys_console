<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 2019-12-01
 * Time: 03:04
 */

namespace backend;


use App\Csv;
use App\Ex_product;
use App\Ex_product_csv;
use App\Jobs\CreateProdcut;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CSVReader
{
    public $format;

    private $id;

    private $mapping;

    private $productRaw;

    const MAPPING = [
        'pb' => ['PB', "PB Price List_ROS0179.csv", [
            'model'=>1,
            'stock'=>3,
            'price'=>4,
            'name'=>2,
            'supplier_code' =>0
        ]],
        'im' => ['Ingram micro', "141970.CSV", [
            'model'=>2,
            'stock'=>7,
            'price'=>5,
            'name'=>1,
            'supplier_code' =>0
        ]],
        'aw'=>['Anywhere', 'AnywareNZ price list  3.csv', [
            'model'=>6,
            'stock'=>3,
            'price'=>4,
            'name'=>1,
            'supplier_code' =>0
        ]],
        'do'=>['Dove', "dealerpricelist.csv",[
            'model'=>1,
            'stock'=>5,
            'price'=>4,
            'name'=>2,
            'supplier_code' =>3
        ]],
        'sy'=>['Synnex', 'ROC_synnex_nz.csv', [
            'model'=>3,
            'stock'=>11,
            'price'=>7,
            'name'=>4,
            'supplier_code' =>1
        ]],
        'cd'=>['Computer Dynamics', 'cdl roctec.csv', [
            'model'=>1,
            'stock'=>10,
            'price'=>8,
            'name'=>2,
            'supplier_code' =>0
        ]],
        'snap'=>['Snapper Network', "snappernet2.csv", [
            'model'=>0,
            'stock'=>9,
            'price'=>6,
            'name'=>4,
            'supplier_code' =>0
        ]],
//            'dj'=>['DJI', null, []],
        'ex'=>['RTEP', "RTEP.csv", [
            'model'=>1,
            'stock'=>6,
            'price'=>3,
            'name'=>7,
            'supplier_code' =>0
        ]],
        'wc'=>['Westcom', "0001037946.csv", [
            'model'=>1,
            'stock'=>8,
            'price'=>7,
            'name'=>2,
            'supplier_code' =>1
        ]],
        'gw'=>['Go Wireless NZ', 'pricelist.csv', [
            'model'=>0,
            'stock'=>9,
            'price'=>5,
            'name'=>3,
            'supplier_code' =>2
        ]],
        'dd'=>['Dicker DATA', "datafeed.csv", [
            'model'=>0,
            'stock'=>9,
            'price'=>8,
            'name'=>3,
            'supplier_code' =>0
        ]],
        'ts'=>['TechStar', "Item List (Summary).csv"],
        'ag'=>['Atlas Gentech', '09072019.csv', [
            'model'=>0,
            'stock'=>4,
            'price'=>3,
            'name'=>2,
            'supplier_code' =>1
        ]]
    ];

    /**
     * @param mixed $productRaw
     */
    public function setProductRaw($productRaw)
    {

        array_walk($productRaw, function(&$value){
            $value = strtolower(str_replace(' ', '', trim(html_entity_decode($value))));
        });
        $this->productRaw = array_flip($productRaw);
    }

    /**
     * @param mixed $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function __construct()
    {
        // format: ['id'=>['name', 'filename', [mapping info]]]
        $this->format = self::MAPPING;
    }

    public static function loadCSVByFile($fileName){
        $csv = new self();
        foreach ($csv->format as $id=>$detail){
            if ($detail[1] == $fileName){
                $csv->setId($id);
                $csv->setMapping($detail[2]);
                return $csv;
            }
        }
        return null;
    }

    public function process($leaveCsvCopy=true){

        if (isset($this->id)){
            $this->clearOldRecords();

            ini_set('memory_limit', -1);

            $this->setProductRaw(Ex_product::whereNotNull('mpn')->pluck('mpn', 'product_id')->all());


            $reader = ReaderEntityFactory::createCSVReader();

            $file = storage_path('csv/'.$this->format[$this->id][1]);
            if ($leaveCsvCopy){
                copy($file, $file.'.processing');

            }else{
                rename($file, $file.'.processing');
            }

            $reader->open($file.'.processing');

            $data = [];
            foreach ($reader->getSheetIterator() as $sheet){
                foreach ($sheet->getRowIterator() as $row){
                    try{
                        $rowData = $this->dataTransformer($row->getCells());

                        if ($this->dataVerification($rowData)){
                            if ($rowData['product_id'] == -1){
                                $product = Product::create($this->newProductTransformer($rowData));
                                $rowData['product_id'] = $product->getProductId();
                            }
                            $data[] = array_filter($rowData, function($k){
                                return in_array($k, [ 'supplier_code','price','stock','model','supplier', 'product_id', 'price_to_sell']);
                            }, 2);

                        }

                    }catch (\Exception $e){
                        Log::error($e->getMessage());
                    }
                }
                break;
            }

            $reader->close();
            DB::connection('extremepc_mysql')->disableQueryLog();
            Ex_product_csv::insert($data);
            unlink($file.'.processing');
            $this->recordCsv();
            return true;
        }else{
            return false;
        }
    }

    public function read($limit = 5){
        if (isset($this->id)){

            $this->setProductRaw(Ex_product::whereNotNull('mpn')->pluck('mpn', 'product_id')->all());

            $reader = ReaderEntityFactory::createCSVReader();

            $file = storage_path('csv/'.$this->format[$this->id][1]);

            copy($file, $file.'.reading');

            $reader->open($file.'.reading');

            $data = [];
            foreach ($reader->getSheetIterator() as $sheet){
                foreach ($sheet->getRowIterator() as $row){
                    try{
                        $rowData = $this->dataTransformer($row->getCells());

                        if ($this->dataVerification($rowData)){

                            $data[] = $rowData;
                            if ($limit-- <= 0){
                                break;
                            }

                        }


                    }catch (\Exception $e){
                        Log::error($e);
                    }
                }
                break;
            }

            $reader->close();
            unlink($file.'.reading');
            return $data;
        }else{
            return null;
        }
    }

    private function dataTransformer($cells){
        $model = $cells[$this->mapping['model']]->getValue();
        $price = $cells[$this->mapping['price']]->getValue();
        $model_clean = strtolower(str_replace(' ', '', trim(html_entity_decode($model))));
        return [
            'product_id' => isset($this->productRaw[$model_clean])?$this->productRaw[$model_clean]:-1,
            'supplier' => $this->id,
            'model' => $model,
            'stock' => $cells[$this->mapping['stock']]->getValue(),
            'price' => $price,
            'price_to_sell' => $this->priceTransformer($price),
            'name' => $cells[$this->mapping['name']].' '.$model,
            'supplier_code' => $cells[$this->mapping['supplier_code']]->getValue(),
        ];
    }

    private function dataVerification($row){
        if (!is_numeric($row['stock']) || $row['stock'] < 1){
            return false;
        }

        if (trim($row['name']) == ''){
            return false;
        }

        return true;
    }

    private function clearOldRecords(){
        Ex_product_csv::where('supplier', $this->id)->delete();
    }

    private function newProductTransformer($rawData){
        return [
            'model' => $rawData['model'],
            'price' => $rawData['price_to_sell'],
            'name' => $rawData['name'],
        ];
    }

    private function priceTransformer($price){
        switch ($this->id)
        {
            default:
                if ($price <= 20){
                    $priceTransformed = ($price+5) * 1.2;
                }elseif ($price <= 100){
                    $priceTransformed = $price * 1.15;
                }elseif ($price <= 500){
                    $priceTransformed = $price * 1.1;
                }elseif ($price <= 1000){
                    $priceTransformed = $price * 1.08;
                }elseif ($price <= 2000){
                    $priceTransformed = $price * 1.06;
                }else{
                    $priceTransformed = $price * 1.05;
                }
                return ceil($priceTransformed*1.15);
        }

    }

    private function recordCsv(){
        Csv::where('supplier_code',$this->id)->delete();
        $csv = new Csv();
        $csv->supplier_code = $this->id;
        $csv->save();
    }

}