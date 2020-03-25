<?php

namespace App\Http\Controllers;

use App\Csv;
use App\Ex_alias;
use App\Ex_category;
use App\Ex_product;
use App\Ex_product_csv;
use App\Ex_product_description;
use App\Ex_product_store;
use backend\CSVReader;
use backend\ExtremepcProduct;
use Carbon\Carbon;
use Faker\Provider\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        View::share('supplier_list',[
            'pb' => 'PB', 
            'im' => 'Ingram micro',
            'aw'=>'Anywhere',
            'do'=>'Dove',
            'sy'=>'Synnex', 
            'cd'=>'Computer Dynamics',
            'snap'=>'Snapper Network',
            'dj'=>'DJI',
            'ex'=>'RTEP',
            'wc'=>'Westcom',
            'gw'=>'Go Wireless NZ',
            'dd'=>'Dicker DATA',
            'ts'=>'TechStar',
            'ag'=>'Atlas Gentech']);


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
            'snap'=>[
                'mpn'=>0,
                'stock'=>'Stock',
                'price'=>'Reseller_Buy',
                'name'=>'Product_Name',
                'supplier_code' =>'Product_Code'
            ],
            'ag'=>[
                'mpn'=>'manufacturer_code',
                'stock'=>'quantityonhand',
                'price'=>'sellingprice',
                'name'=>'itemname',
                'supplier_code' =>'itemnumber'
            ],
            'ex'=>[
                'mpn'=>'manufacturerproductcode',
                'stock'=>'stockonhand',
                'price'=>'priceexgst',
                'name'=>'productdescription',
                'supplier_code' =>'productcode'
            ],
            'wc'=>[
                'mpn'=>'item_number',
                'stock'=>'qoh',
                'price'=>'customer_price',
                'name'=>'item_name',
                'supplier_code' =>'item_number'
            ],
            'dd'=>[
                'mpn'=>'stockcode',
                'stock'=>'stockavailable',
                'price'=>'dealerex',
                'name'=>'stockdescription',
                'supplier_code' =>'stockcode'
            ],
            'gw'=>[
                'mpn'=>'mpn',
                'stock'=>'stock_status',
                'price'=>'your_price',
                'name'=>'product_name',
                'supplier_code' =>'unique_internal_code'
            ],
            'ts'=>[
                'mpn'=>'item_no.',
                'stock'=>'units_on_hand',
                'price'=>'current_price',
                'name'=>'item_description',
                'supplier_code' =>'item_no.'
            ],
            'dj'=>[
                'mpn'=>'part',
                'stock'=>'stock',
                'price'=>'12_margin_buy',
                'name'=>'item_name',
                'supplier_code' =>'part'
            ],

        );
    }

    public function index(){
        return view('csv.index');
    }

    public function batchUpload(Request $request){
        $lists = [
            "141970.CSV" => "im",
            "AnywareNZ price list 3.csv" => "aw",
            "CDL daily Pricefile.csv" => "cd",
            "snappernet2.csv" => "snap",
            "dealerpricelist.csv" => "do",
            "PB Price List_ROS0179.csv" => "pb",
            "ROC_synnex_nz.csv" => "sy",
            "RTEP.csv" => "ex",
            "0001037946.csv"=>"wc",
            "pricelist.csv"=>"gw",
            "datafeed.csv"=>"dd",
            "Item List (Summary).csv"=>"ts",
            "09072019.csv"=>"ag"
            
        ];
        $uploads = [];
        foreach($request->file('csvs') as $file){
            if(isset($lists[$file->getClientOriginalName()])){
                $file->move(storage_path('app/csv'),$lists[$file->getClientOriginalName()].'.csv');
                $uploads[] = $lists[$file->getClientOriginalName()];
            }
        }
        $request->session()->flash('success',implode(' | ',$uploads).' '.count($uploads).' csv files upload successfully!');
        return redirect()->back();

    }

    public function run(Request $request){
        $this->validate($request,[
            'csv'=>'required',
            'supply_code'=>'required'
        ]);
        $supply_code = $request->input('supply_code');
        $filename = "$supply_code.csv";
       $request->file('csv')->move(storage_path('app/csv'),$filename);
       try{
           $firstsheet = 'test';
           Excel::load('storage/app/csv/'.$filename,function ($render) use(&$firstsheet){
               $firstsheet = $render->first();
           });
//           dd($firstsheet);
           $firstsheet = $this->dataMap($supply_code,$firstsheet);

           return view('csv.index',compact('firstsheet','supply_code'));
       }catch (\Exception $e){
           $firstsheet = $e->getMessage();
           return view('csv.index',compact('firstsheet'));
       }

    }

    public function clear(){

        if (Input::has('type')&&Input::get("type")==1){
            Ex_category::find(NOCATEGORY)->products()->chunk(100,function ($products){
                foreach ($products as $product){
                    if($product->csvs()->count()<1){
                        $product->status=0;
                        $product->save();
                    }
                }
            });
        }else{
            Ex_product::whereNotNull('mpn')->where('mpn','<>','')->where('quantity','<',1)->chunk(100,function ($products){
                foreach ($products as $product){
                    if($product->csvs()->count()<1){
                        $product->status=0;
                        $product->save();
                    }
                }
            });
        }

        return Carbon::now().': success!';
    }

    public function deleteDisable(){
        foreach (Ex_product::where('status',0)->cursor() as $product){
            $product->description()->delete();
            $product->special()->delete();
            Ex_alias::where('query','product_id='.$product->product_id)->delete();
            $product->delete();
        }
        return Carbon::now().': success!';
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

    public function batchImport(){
        ini_set('max_execution_time',0);
        foreach(glob(storage_path('app/csv/*.csv')) as $file){
            $file = str_replace(storage_path('app/csv/'),'',$file);
            $file = str_replace('.csv','',$file);
            $this->startImport($file,storage_path('app/csv/'));
        }
        return $this->clear();
    }

    public function startImport($supply_code,$path=''){
        if ($path===''){
            $path = storage_path('app/csv/');
        }
//        $this->$supply_code();
        DB::beginTransaction();
        try{
            $this->cleanProductCscByCode($supply_code);

            $mpn_map = $this->map[$supply_code]['mpn'];
            $stock_map = $this->map[$supply_code]['stock'];
            $name_map = $this->map[$supply_code]['name'];
            $price_map = $this->map[$supply_code]['price'];
            $supply_code_map  = $this->map[$supply_code]['supplier_code'];
            Excel::filter('chunk')->load($path.$supply_code.'.csv')->chunk(5000, function($results) use ($supply_code,$mpn_map,$stock_map,$name_map,$price_map,$supply_code_map)
            {
                foreach($results as $row)
                {

                    $this->importSingleProduct($row->$mpn_map,$row->$stock_map,$row->$price_map,$supply_code,$row->$name_map.' '.$row->$mpn_map,$row->$supply_code_map);
                }
            });
            $this->recordCsv($supply_code);
            Ex_product::where('status',1)->whereNotNull('mpn')->where('mpn','<>','')->where('quantity','<',1)->chunk(100,function ($products){
                foreach ($products as $product){
                    $this->price_update($product);
                }
            });
            DB::commit();
            unlink($path.$supply_code.'.csv');
            return redirect('csv/import');
        }catch (\Exception $e){
            DB::rollback();
            print_r($e->getLine());
            print_r($e->getMessage());
        }

    }

    private function recordCsv($code){
        Csv::where('supplier_code',$code)->delete();
        $csv = new Csv();
        $csv->supplier_code = $code;
        $csv->save();
    }

//    private function pb(){
//        DB::beginTransaction();
//        try{
//            $this->cleanProductCscByCode('pb');
//            Excel::filter('chunk')->load('storage/app/pb.csv')->chunk(100, function($results)
//            {
//
//                foreach($results as $row)
//                {
//
//                    $this->importSingleProduct($row->manufacturers_code,$row->bulk_stock,$row->your_price,'pb',$row->product_name.' '.$row->manufacturers_code,$row->pb_part_number);
//                }
//
//            });
//            $this->recordCsv('pb');
//            DB::commit();
//        }catch (\Exception $e){
//            DB::rollback();
//            echo $e->getMessage();
//        }
//    }
    public function testPrice($id){
//        return $this->price_update(Ex_product::find($id));
//        Ex_product::where('status',1)->where('price','>',99990)->chunk(100,function ($products){
//            foreach ($products as $product){
//                if($product->quantity>0){
//                    echo '<label style="color:red">'.$product->mpn.'</label>';
//                }else{
//                    echo $product->mpn;
//                }
//
//                echo '-';
//                $this->price_update($product);
//                echo '<br>';
//
//            }
//        });

//        Ex_product::where('status',1)->where('price','>',99990)->chunk(50,function ($products){
//            foreach ($products as $product){
//                $ap = $this->grabProductCost($product->model);
//                if ($ap<1){
//                    echo $product->model;
//                    echo '<br>';
//                }else{
//                    $product->price = $this->pricePrettify($ap*1.5);
//                    $product->save();
//                }
//
//            }
//
//        });
        Ex_product::where('status',1)->where('quantity','>',0)->chunk(100,function ($products){
            foreach ($products as $product){
                $product->price = $this->pricePrettify($product->price);
                $product->save();
            }
        });

    }
    private function pricePrettify($price,$skip=true){
        $price = $price*1.15;
        if ($skip){
            $price = $price/10;
            $price = floor($price);
            $price = $price*10+9;
        }else{
            $price = ceil($price);
        }

        return $price/1.15;
    }
    private function importSingleProduct($mpn,$stock,$price,$supply_code,$name,$supplier_code){
        $stock = intval(str_replace('+', '', trim($stock)));
        if ($stock<1){
            return false;
        }
        $price = str_replace('$','',$price);
        $price = trim($price);

        if (!is_numeric($price) || trim($mpn)==''){
            return false;
        }
        if($supply_code=='ts'){
            if ($price < 10){
                $price = $price+2;
            }else{
                $price = $price/0.6;
            }
        }
        if(stripos('#ra',$supplier_code)!==false){
            return false;
        }

        $mpn = trim($mpn);

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

    public function selfCheck(){
        echo "<h3>Special Error</h3>";
        Ex_product::where('status',1)->chunk(5000,function ($products){
            foreach ($products as $product){
                if (!is_null($special = $product->special) && $special->price>=$product->price){
                    echo $product->model.'<br>';
                }
            }
        });
        echo '<hr>';
        echo "<h3>Disable but still has quatity</h3>";
        Ex_product::where('status',0)->chunk(5000,function ($products){
            foreach ($products as $product){
                if ($product->quantity!=0){
                    echo $product->model.'<br>';
                }
            }
        });
    }

    private function price_update($product){
        $product_price = Ex_product_csv::where('product_id',$product->product_id)->where('stock','>',0)->min('price');
            if (is_numeric($product_price)){
                $exproduct = ExtremepcProduct::find($product->product_id);
                $exproduct->setPrice($this->pricePrettify($this->generatePrice($product_price),false));
            }



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
            foreach ($products as $product){
                $product->status=1;
                $product->save();
            }
            return $products->pluck('product_id');
        }else{
            return false;
        }
    }

    private function generatePrice($price){
        if ($price < 0 || !is_numeric($price)){
            return 99999;
        }
        /*if ($price < 20){
            return $price+2;
        }elseif ($price < 100){
            return $price*1.1;
        }elseif ($price < 300){
            return $price*1.08;
        }elseif ($price < 1000){
            return $price*1.07;
        }else{
            return $price*1.06;
        }*/

         if ($price < 20){
            return $price+2
;        }elseif ($price < 100){
            return $price*1.15;
        }elseif ($price < 300){
            return $price*1.13;
        }elseif ($price < 1000){
            return $price*1.12;
        }else{
            return $price*1.11;
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

    public function grabProductCost($code){
        $url = env('SNPORT') . "?action=test&code=$code";

        $pricedetail = $this->getContent($url);

        $averageCost = null;
        if(str_contains($pricedetail,'Average price inc')){
            $productDetailArray = explode('<br>',$pricedetail);
            $averageCost = str_replace('Average Cost: $','',$productDetailArray[4]);
            $averageCost = str_replace(',','',$averageCost);
            $averageCost = floatval($averageCost);
            $averageCost = $averageCost;
            $averageCost = round($averageCost,2);
        }else{
            $averageCost = 0;
        }
        return $averageCost;
    }
    private function getContent($url)
    {

        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }

    public function delSeo(){

        Ex_alias::chunk(400,function ($items){
            foreach ($items as $item){

                $id = str_replace('product_id=','',$item->query);
                if (is_numeric(trim($id))){
                    if(is_null(Ex_product::find($id))){
                        $item->delete();
                    }
                }

            }
        });
    }

    public function saveCsv(Request $request){
        $from = $request->get('FROM');
        $csv = [];
        foreach ($request->allFiles() as $file){
            $filename = $file->getClientOriginalName();
            Log::info('receive '.$filename);
            foreach (CSVReader::MAPPING as $supplier){
                if ($filename == $supplier[1]){
                    $file->move(storage_path("csv"), $filename);
                    $csv[] = "$supplier[0] csv file received, file name: $filename";
                }
            }
        }
        Mail::raw(implode('\r\n',$csv), function ($message) {
            $message->from('sales@extremepc.co.nz');
            $message->to('AKL.Support@extremepc.co.nz', 'Extremepc');
            $message->bcc('hugowangchn@gmail.com', 'Hugo Wang');
            $message->subject('New CSV file received at '.Carbon::now());
        });
        return response('Success', 200);
    }
}
