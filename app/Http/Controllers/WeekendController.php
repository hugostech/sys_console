<?php

namespace App\Http\Controllers;

use App\Ex_category;
use App\Ex_product;
use App\WeekendSale;
use backend\ExtremepcProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
const TARGETCATEGORY=425;
const DISPLAYCATEGORY=423;
class WeekendController extends Controller
{
    public function index(){
        $products = [];
        if (Input::has('a') && Input::get('a')=='import'){
            $products = $this->all();
        }
        $weekendsale = WeekendSale::all();
        $editing_model = false;
        return view('weeksale.index',compact('products','weekendsale','editing_model'));
    }

    public function get($model){
        $target = Ex_product::where('model',$model)->first();

        $product = [];
        if (!is_null($target)){
            $product = [$target->product_id];

        }
        return $product;

    }

    public function all(){
        $products = [];
        foreach (Ex_category::find(TARGETCATEGORY)->products()->where('status',1)->pluck('oc_product.product_id')->all() as $id){
            $products[$id] = $this->findProductData($id);
        }
        return $products;
    }

    private function findProductData($id){
        $product = ExtremepcProduct::find($id);
        $item = [];
        $item['model'] = $product->product->sku;
        $item['name'] = $product->product->description->name;
        $item['price_current'] = round($product->product->price*1.15,2);
        $item['special_current'] = round($product->getSpecial()*1.15,2);
        $tem = $product->info();
        $item['cost'] = round($tem['averagecost']*1.15,2);
        $item['stock'] = $tem['stock'];
        $item['lock_status'] = $product->product->price_lock;
        $item['sale_base'] = round($product->product->price*1.15,2);
        $item['sale_special'] = ceil($tem['averagecost']*1.05*1.15);
        return $item;
    }
    public function create(Request $request){
        $this->validate($request, [
            'base'=>'required',
            'special'=>'required',
        ]);
        $products = [];
        foreach ($request->base as $id=>$price){
            $products[$id] = [$price,$request->special[$id]];
        }
        $sale = new WeekendSale();
        $sale->products = \GuzzleHttp\json_encode($products);
        $sale->end_date = $request->has('end_date')?Carbon::parse($request->end_date):Carbon::now()->next(Carbon::MONDAY);
        $sale->save();
        return redirect('weekendsale');
    }

    public function show($id){
        $sale = WeekendSale::find($id);
        $sale_id = $id;
        $end_date = Carbon::parse($sale->end_date)->format('Y-m-d');
        $products = [];
        foreach (json_decode($sale->products,true) as $id=>$prices){
            $product = $this->findProductData($id);
            $product['sale_base'] = $prices[0];
            $product['sale_special'] = $prices[1];
            $products[$id] = $product;
        }
        $weekendsale = WeekendSale::all();
        $editing_model = true;

        return view('weeksale.index',compact('products','weekendsale','editing_model','sale_id','end_date'));
    }

    public function addProduct(Request $request){
        $this->validate($request, [
            'product_new'=>'required'
        ]);
        $id = $request->get('sale_id');
        $sale = WeekendSale::find($id);
        $sale_id = $id;
        $end_date = Carbon::parse($sale->end_date)->format('Y-m-d');
        $products = [];
        foreach (json_decode($sale->products,true) as $id=>$prices){
            $product = $this->findProductData($id);
            $product['sale_base'] = $prices[0];
            $product['sale_special'] = $prices[1];
            $products[$id] = $product;
        }
        $new_product = $this->get(trim($request->get('product_new')));
        if (count($new_product)>0 && !array_key_exists($new_product[0], $products)){
            $products[$new_product[0]] = $this->findProductData($new_product[0]);
        }
        $weekendsale = WeekendSale::all();
        $editing_model = true;

        return view('weeksale.index',compact('products','weekendsale','editing_model','sale_id','end_date'));

    }

    public function update(Request $request){
        $this->validate($request, [
            'sale_id'=>'required',
            'base'=>'required',
            'special'=>'required',
        ]);
        $products = [];
        foreach ($request->base as $id=>$price){
            $products[$id] = [$price,$request->special[$id]];
        }
        $sale = WeekendSale::find($request->get('sale_id'));
        $sale->products = \GuzzleHttp\json_encode($products);
        $sale->end_date = $request->has('end_date')?Carbon::parse($request->end_date):Carbon::now()->next(Carbon::MONDAY);
        $sale->save();
        return redirect('weekendsale');
    }

    public function del($id){
        $sale = WeekendSale::find($id);
        if (is_null($sale)){
            Session::flash('alert-danger','The sales is not existing!');
            return redirect('weekendsale');
        }

        if ($sale->status == 1){
            Session::flash('alert-danger','The sale is running, stop it first!');
        }else{
            $sale->delete();
        }
        return redirect('weekendsale');
    }

    public function up($id){

        $sale = WeekendSale::find($id);
        if ($sale->status==1){
            Session::flash('alert-danger','This sale is running');
        }else{
            if (WeekendSale::where('status',1)->count()>0){
                Session::flash('alert-danger','There is sale running, stop it first!');
            }else{
                $products = json_decode($sale->products,true);
                $product_ids = array_keys($products);
                Ex_category::find(DISPLAYCATEGORY)->products()->sync($product_ids);
                foreach ($products as $id=>$prices){
                    try{
                        $product = ExtremepcProduct::find($id);
                        if($product->record()){
                            $product->unlock();
                            $product->setPrice($prices[0],true);
                            $product->setSpecial($prices[1],true,$sale->end_date);
                            $product->lock();
                        }
                    }catch (\Exception $e){
                        Log::error($e->getMessage());
                    }
                }
                $sale->start_date = Carbon::now();
                $sale->status = 1;
                $sale->save();
            }

        }

        return redirect('weekendsale');
    }

    public function down($id){
        $sale = WeekendSale::find($id);
        if ($sale->status == 1){
            $products = json_decode($sale->products,true);
            foreach ($products as $id=>$prices){
                try{
                    $product = ExtremepcProduct::find($id);
                    $product->unlock()->restore();
                }catch (\Exception $e){
                    Log::error($e->getMessage());
                }
            }
            Ex_category::find(DISPLAYCATEGORY)->products()->sync([]);
            $sale->start_date = null;
            $sale->status = 0;
            $sale->save();
        }else{
            Session::flash('alert-danger','This sale is not running.');
        }

        return redirect('weekendsale');
    }


}
