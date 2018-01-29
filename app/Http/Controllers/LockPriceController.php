<?php

namespace App\Http\Controllers;

use App\Ex_product;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class LockPriceController extends Controller
{
    public function lockPrice($id){
        $product = Ex_product::find($id);
        if (!is_null($product)){
            $product->price_lock = 1;
            $product->save();
            if (Input::has('r')){
                return redirect()->back();
            }
            return \GuzzleHttp\json_encode(['Resule'=>'Price Locked']);
        }else{
            return abort(403, 'Product cannot find');
        }
    }

    public function unlockPrice($id){
        $product = Ex_product::find($id);
        if (!is_null($product)){
            $product->price_lock = 0;
            $product->save();
            if (Input::has('r')){
                return redirect()->back();
            }
            return \GuzzleHttp\json_encode(['Resule'=>'Price Unlocked']);
        }else{
            return abort(403, 'Product cannot find');
        }
    }

    public function listProduct(){
        $products = Ex_product::where('status',1)->where('price_lock',1)->get();
        return view('lockprice.index',compact('products'));
    }
}
