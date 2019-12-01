<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 2019-12-01
 * Time: 03:04
 */

namespace backend;


use App\Ex_product_csv;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CSVReader
{
    public $format;

    private $id;

    private $mapping;

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
        $this->format = [
            'pb' => ['PB', "PB Price List_ROS0179.csv", []],
            'im' => ['Ingram micro', "141970.CSV", [
                'model'=>2,
                'stock'=>7,
                'price'=>5,
                'name'=>1,
                'supplier_code' =>0
            ]],
            'aw'=>['Anywhere', 'AnywareNZ price list 3.csv'],
            'do'=>['Dove', "dealerpricelist.csv"],
            'sy'=>['Synnex', 'ROC_synnex_nz.csv'],
            'cd'=>['Computer Dynamics', 'CDL daily Pricefile.csv'],
            'snap'=>['Snapper Network', "snappernet2.csv"],
//            'dj'=>['DJI', null, []],
            'ex'=>['RTEP', "RTEP.csv"],
            'wc'=>['Westcom', "0001037946.csv"],
            'gw'=>['Go Wireless NZ', 'pricelist.csv'],
            'dd'=>['Dicker DATA', "datafeed.csv"],
            'ts'=>['TechStar', "Item List (Summary).csv"],
            'ag'=>['Atlas Gentech', '09072019.csv']
        ];
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

    public function process(){

        if (isset($this->id)){
            $this->clearOldRecords();

            ini_set('memory_limit', -1);

            $reader = ReaderEntityFactory::createCSVReader();

            $file = storage_path('csv/'.$this->format[$this->id][1]);
            rename($file, $file.'.processing');

            $reader->open($file.'.processing');

            $data = [];
            foreach ($reader->getSheetIterator() as $sheet){
                foreach ($sheet->getRowIterator() as $row){
                    try{
                        $rowData = $this->dataTransformer($row->getCells());

                        if ($this->dataVerification($rowData)){
                            $data[] = $rowData;
                        }

                    }catch (\Exception $e){
                        Log::error($e->getMessage());
                    }
                }
                break;
            }

            $reader->close();

            print_r(count($data));
            unset($data[0]);
            DB::connection('extremepc_mysql')->disableQueryLog();
            Ex_product_csv::insert($data);

            unlink($file.'.processing');

            return true;
        }else{
            return false;
        }
    }

    private function dataTransformer($cells){
        return [
            'supplier' => $this->id,
            'model' => $cells[$this->mapping['model']]->getValue(),
            'stock' => $cells[$this->mapping['stock']]->getValue(),
            'price' => $cells[$this->mapping['price']]->getValue(),
//            'name' => $cells[$this->mapping['name']].' '.$cells[$this->mapping['mpn']],
            'supplier_code' => $cells[$this->mapping['supplier_code']]->getValue(),
        ];
    }

    private function dataVerification($row){
        if ($row['stock'] < 1){
            return false;
        }

        return true;
    }

    private function clearOldRecords(){
        Ex_product_csv::where('supplier', $this->id)->delete();
    }

}