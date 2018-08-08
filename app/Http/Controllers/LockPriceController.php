<?php

namespace App\Http\Controllers;

use App\Ex_manufacturer;
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

    public function lockByBrand(){
        $manufacturers = Ex_manufacturer::pluck('name','manufacturer_id');
        return view('lockprice.lockByBrand',compact('manufacturers'));
    }

    public function lockProductsByBrand(Request $request){
        $this->validate($request, [
            'brand'=>'required',
            'status'=>'required'
        ]);
        try{
            $this->updateProductsByBrand($request->get('brand'),$request->get('status'));
            return redirect()->back();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function updateProductsByBrand($id, $status){
        Ex_manufacturer::where('manufacturer_id',$id)->update(['price_lock'=>$status]);

    }
}
