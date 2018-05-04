<?php

namespace App\Http\Controllers;

use App\Ex_category;
use backend\ExtremepcProduct;
use Illuminate\Http\Request;

use App\Http\Requests;
const TARGETCATEGORY=423;
class WeekendController extends Controller
{
    public function index(){
        return view('weeksale.index');
    }

    public function get($id){
        $product = ExtremepcProduct::find($id);
        dd($product->info());
    }

    public function all(){
        $products = [];
        foreach (Ex_category::find(TARGETCATEGORY)->products()->where('status',1)->pluck('oc_ex_product.product_id')->all() as $id){
            $product = ExtremepcProduct::find($id);
            $item = [];
            $item['product_id'] = $id;
            $item['model'] = $product->product->model;
            $item['name'] = $product->product->description->name;
            $item['price_current'] = round($product->product->price*1.15,2);
            $item['special_current'] = round($product->getSpecial()*1.15,2);
            $tem = $product->info();
            $item['cost'] = round($tem['averagecost']*1.15,2);
            $item['stock'] = $tem['stock'];
            $item['lock_status'] = $product->product->price_lock;
            $products[] = $item;
        }
        return \GuzzleHttp\json_encode($products);
    }


}
