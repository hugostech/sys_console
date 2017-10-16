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
class CsvController extends Controller
{
    private $category;
    public function __construct()
    {
        $this->category = Ex_category::find(NOCATEGORY);

        View::share('csvRecords',Csv::all()->toArray());
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
               $firstsheet = $render->first()->toArray();

           });
           return view('csv.index',compact('firstsheet','supply_code'));
       }catch (\Exception $e){
           $firstsheet = $e->getMessage();
           return view('csv.index',compact('firstsheet'));
       }

    }

    public function startImport($supply_code){

        $this->$supply_code();
        return redirect('csv/import');
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


    public function importSingleProduct($mpn,$stock,$price,$supply_code,$name,$supplier_code){
        $mpn = trim($mpn);
        if ($products = $this->mapProductByMpn($mpn)){
            foreach ($products as $product_id){
                $this->recordProductCsv($product_id,$stock,$price,$supply_code,$supplier_code);
            }
        }else{
            $product_id = $this->generateProduct($mpn,$name,$price);
            $this->recordProductCsv($product_id,$stock,$price,$supply_code,$supplier_code);
        }

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
        if ($price < 20){

        }
    }

    private function recordProductCsv($product_id,$stock,$price,$supply_code,$supplier_code){
        Ex_product_csv::create(compact('product_id','price','stock','supply_code','supplier_code'));
    }

    private function cleanProductCscByCode($supply_code){
        Ex_product_csv::where('supply_code',$supply_code)->delete();
    }

    private function generateProduct($mpn,$name,$price){
        //to do price edit
        //out of stock status edit
        //brand
        $tem = array(
            'model' => $mpn,
            'mpn' => $mpn,
            'quantity' => 0,
            'stock_status_id' => 9,
            'shipping' => 1,
            'price' => $price*1.1,
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
