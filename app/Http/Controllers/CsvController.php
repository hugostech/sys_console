<?php

namespace App\Http\Controllers;

use App\Csv;
use App\Ex_category;
use App\Ex_product;
use App\Ex_product_csv;
use App\Ex_product_description;
use App\Ex_product_store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;

Define('NOCATEGORY',381);
Define('OUTOFSTOCK',5);
Define('ONLINEONLY',9);
class CsvController extends Controller
{
    private $category;
    private $map;
    public function __construct()
    {
        $this->category = Ex_category::find(NOCATEGORY);

        View::share('csvRecords',Csv::all()->toArray());
//        View::share('csvRecords',[]);
        View::share('supplier_list',['pb' => 'PB', 'im' => 'Ingram micro','aw'=>'Anywhere','do'=>'Dove','sy'=>'Synnex','cd'=>'Computer Dynamics']);


        $this->map = array(
            'pb'=>[
                'mpn'=>'manufacturers_code',
                'stock'=>'bulk_stock',
                'price'=>'your_price',
                'name'=>'product_name',
                'supplier_code' =>'pb_part_number'
            ],
            'aw'=>[
                'mpn'=>'manufacturer_code',
                'stock'=>'quantityonhand',
                'price'=>'sellingprice',
                'name'=>'itemname',
                'supplier_code' =>'itemnumber'
            ],
            'do'=>[
                'mpn'=>'mftr_code',
                'stock'=>'qty',
                'price'=>'price',
                'name'=>'description',
                'supplier_code' =>'dove_code'
            ],
            'im'=>[
                'mpn'=>'vendor_part_number',
                'stock'=>'available_quantity',
                'price'=>'customer_price',
                'name'=>'ingram_part_description',
                'supplier_code' =>'ingram_part_number'
            ],
            'sy'=>[
                'mpn'=>'partno',
                'stock'=>'stock',
                'price'=>'block1_price',
                'name'=>'description',
                'supplier_code' =>'gcode'
            ],
            'cd'=>[
                'mpn'=>0,
                'stock'=>'qty_in_stock',
                'price'=>'dealer',
                'name'=>'description',
                'supplier_code' =>'part_code'
            ],

        );
    }

    public function index(){
        return view('csv.index');
    }

    public function run(Request $request){
        $this->validate($request,[
            'csv'=>'required',
            'supply_code'=>'required'
        ]);
        $supply_code = $request->input('supply_code');
        $filename = "$supply_code.csv";
       $request->file('csv')->move(storage_path('app'),$filename);
       try{
           $firstsheet = 'test';
           Excel::load('storage/app/'.$filename,function ($render) use(&$firstsheet){
               $firstsheet = $render->first();
           });
           $firstsheet = $this->dataMap($supply_code,$firstsheet);

           return view('csv.index',compact('firstsheet','supply_code'));
       }catch (\Exception $e){
           $firstsheet = $e->getMessage();
           return view('csv.index',compact('firstsheet'));
       }

    }

    public function clear(){
        $this->category->products()->chunk(100,function ($products){
            foreach ($products as $product){
                if($product->csvs()->count()<1){
                    $product->delete();
                }
            }
        });
        return 'success!';
    }

    private function dataMap($code,$data){
        if(isset($this->map[$code])){
            $result = [];
            $map = $this->map[$code];
            foreach ($map as $value){
                $result[]=$data->$value;
            }
            return $result;
        }else{
            return ['error'=>'Supplier code not varified'];
        }
    }

    public function startImport($supply_code){

//        $this->$supply_code();
        DB::beginTransaction();
        try{
            $this->cleanProductCscByCode($supply_code);

            $mpn_map = $this->map[$supply_code]['mpn'];
            $stock_map = $this->map[$supply_code]['stock'];
            $name_map = $this->map[$supply_code]['name'];
            $price_map = $this->map[$supply_code]['price'];
            $supply_code_map  = $this->map[$supply_code]['supplier_code'];
            Excel::filter('chunk')->load('storage/app/'.$supply_code.'.csv')->chunk(100, function($results) use ($supply_code,$mpn_map,$stock_map,$name_map,$price_map,$supply_code_map)
            {

                foreach($results as $row)
                {

                    $this->importSingleProduct($row->$mpn_map,$row->$stock_map,$row->$price_map,$supply_code,$row->$name_map.' '.$row->$mpn_map,$row->$supply_code_map);
                }

            });
            $this->recordCsv($supply_code);
//            $category = Ex_category::find(NOCATEGORY);
            $this->category->products()->chunk(100,function ($products){
                foreach ($products as $product){
                    $this->price_update($product);
                }
            });
            DB::commit();
            return redirect('csv/import');
        }catch (\Exception $e){
            DB::rollback();
            echo $e->getFile().' '.$e->getLine().'-'.$e->getMessage();
        }

    }

    private function recordCsv($code){
        Csv::where('supplier_code',$code)->delete();
        $csv = new Csv();
        $csv->supplier_code = $code;
        $csv->save();
    }

    private function pb(){
        DB::beginTransaction();
        try{
            $this->cleanProductCscByCode('pb');
            Excel::filter('chunk')->load('storage/app/pb.csv')->chunk(100, function($results)
            {

                foreach($results as $row)
                {

                    $this->importSingleProduct($row->manufacturers_code,$row->bulk_stock,$row->your_price,'pb',$row->product_name.' '.$row->manufacturers_code,$row->pb_part_number);
                }

            });
            $this->recordCsv('pb');
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            echo $e->getMessage();
        }
    }


    private function importSingleProduct($mpn,$stock,$price,$supply_code,$name,$supplier_code){
        if (!is_numeric($price) || trim($mpn)==''){
            return false;
        }

        $mpn = trim($mpn);
        if (!is_numeric($stock)){
            $stock=0;
        }


        if ($products = $this->mapProductByMpn($mpn)){

        }else{
            $product_id = $this->generateProduct($mpn,$name,$price);
            $products = [$product_id];
        }

        foreach ($products as $product_id){
            $this->recordProductCsv($product_id,$stock,$price,$supply_code,$supplier_code);
            $this->stock_status_update($product_id);

        }

    }

    private function price_update($product){
        $product_price = Ex_product_csv::select(DB::raw('MIN(price) as price'))->where('product_id',$product->product_id)->first();
        $product->price = $this->generatePrice($product_price->price);
        $product->save();


    }
    private function stock_status_update($product_id){
        $product_stock = Ex_product_csv::select(DB::raw('MAX(stock) as stock'))->where('product_id',$product_id)->first();
        $product = Ex_product::find($product_id);
        $product->stock_status_id =$product_stock->stock>0?ONLINEONLY:OUTOFSTOCK;
        $product->save();

    }
    private function mapProductByMpn($mpn){
        $products = Ex_product::where('mpn',$mpn)->get();
        if(count($products)>0){
            return $products->pluck('product_id');
        }else{
            return false;
        }
    }

    private function generatePrice($price){
        if ($price < 0 || !is_numeric($price)){
            return 99999;
        }
        if ($price < 20){
            return $price+2;
        }elseif ($price < 100){
            return $price*1.15;
        }elseif ($price < 300){
            return $price*1.12;
        }else{
            return $price*1.1;
        }
    }

    private function recordProductCsv($product_id,$stock,$price,$supply_code,$supplier_code){
        Ex_product_csv::create(compact('product_id','price','stock','supply_code','supplier_code'));
    }

    private function cleanProductCscByCode($supply_code){
        Ex_product_csv::where('supply_code',$supply_code)->delete();
    }

    private function generateProduct($mpn,$name,$price){
        //brand
        $price = $this->generatePrice($price);
        $tem = array(
            'model' => $mpn,
            'mpn' => $mpn,
            'quantity' => 0,
            'stock_status_id' => 9,
            'shipping' => 1,
            'price' => $price,
            'tax_class_id' => 9,
            'weight' => 1,
            'weight_class_id' => 1,
            'subtract' => 1,
            'sort_order' => 1,
            'status' => 1,
            'date_added' => Carbon::now()


        );
        $product = Ex_product::create($tem);
        $store = new Ex_product_store();
        $store->product_id = $product->product_id;
        $store->store_id = 0;
        $store->save();
        $description = New Ex_product_description();
        $description->product_id = $product->product_id;
        $description->language_id = 1;
        $description->name = $name;
        $description->description = $name;
        $description->meta_title = $name;
        $description->save();
        $this->category->products()->attach($product->product_id);
        return $product->product_id;
    }
}
