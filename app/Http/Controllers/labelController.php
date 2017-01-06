<?php

namespace App\Http\Controllers;

use App\Ex_product;
use App\Label;
use Illuminate\Http\Request;

use App\Http\Requests;

class labelController extends Controller
{
    public function show_label_tool(){
        return view('label.labelTemplate');
    }

    public function findLabel(Request $request){
        $label = null;
        if($request->has('code')){
            $label = Label::where('code',$request->input('code'))->first();
            $product = Ex_product::where('model',$request->input('code'))->first();
            if(is_null($label)){
                $ex_description = $product->description;

                $label = new Label();
                $label->code = $request->input('code');
                $label->description = $ex_description->description;
                $label->price = round(count($product->special)>0?$product->sepcial->price*1.15:$product->price*1.15,2);
                $label->save();

            }else{
                $label->price = round(count($product->special)>0?$product->sepcial->price*1.15:$product->price*1.15,2);
                $label->save();
            }
        }
        return view('label.labelTemplate',compact('label'));
    }
}
