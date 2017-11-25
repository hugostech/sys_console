<?php

namespace App\Http\Controllers;

use App\Ex_product;
use Illuminate\Http\Request;

use App\Http\Requests;

class LockPriceController extends Controller
{
    public function lockPrice($id){
        $product = Ex_product::find($id);
        if (!is_null($product)){
            $product->price_lock = 1;
            $product->save();
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
            return \GuzzleHttp\json_encode(['Resule'=>'Price Unlocked']);
        }else{
            return abort(403, 'Product cannot find');
        }
    }
}
