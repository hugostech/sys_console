<?php

namespace App\Http\Controllers;

use App\Ex_product;
use App\Label;
use Illuminate\Http\Request;

use App\Ex_speceal;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Ex_category;

class labelController extends Controller
{
    public function show_label_tool(){
        return view('label.labelTemplate');
    }

    public function findLabel(Request $request){
        $label = null;

        if($request->has('code')){
            $label = Label::where('code',$request->input('code'))->first();
            $product = Ex_product::where('sku',$request->input('code'))->first();
            $special = Ex_speceal::where('product_id', $product->product_id)->first();
            if(is_null($label)){
                $ex_description = $product->description;

                $label = new Label();
                $label->code = $request->input('code');
                $label->description = $ex_description->name;
                $label->price = round($product->price * 1.15, 2);
                $label->save();

            }else{
                $label->price = round($product->price  * 1.15, 2);

                $label->save();
            }
        }

        return view('label.labelTemplate',compact('label','product','special'));
    }

    public function editLabel(Request $request){
        $this->validate($request,[
            'label_id'=>'required',

        ]);

        $label = Label::find($request->input('label_id'));
        $tem = null;
        if ($label->type==1 && $request->input('type')==2){
            $tem = array(
                $label->description
            );
            $tem = \GuzzleHttp\json_encode($tem);
        }
        if ($label->type==2 && $request->input('type')==1){
            $tem = \GuzzleHttp\json_decode($label->description,true);
            $tem = implode(' ',$tem);

        }
        $label->update($request->all());
        if (!is_null($tem)){
            $label->description = $tem;
            $label->save();
        }


        $product = Ex_product::where('sku',$label->code)->first();
        $special = Ex_speceal::where('product_id', $product->product_id)->first();
        return view('label.labelTemplate',compact('label','product','special'));

    }

    public function addLabel2PrintList($id){

        $label = Label::find($id);
        $label->prepare2print = 1;
        $label->save();
        $product = Ex_product::where('sku',$label->code)->first();
        $special = Ex_speceal::where('product_id', $product->product_id)->first();

        return view('label.labelTemplate',compact('label','product','special'));

    }


    public function labelList(){
        $labels = Label::where('prepare2print',1)->paginate(16);


        if(Input::has('print')){
            if (Input::has('long')){
                $labels = Label::where('prepare2print',1)->where('type',2)->paginate(16);
                return view('label.longprint',compact('labels'));
            }else{
                $labels = Label::where('prepare2print',1)->where('type',1)->paginate(16);
                return view('label.print',compact('labels'));
            }

        }


        return view('label.labelList',compact('labels'));



    }

    public function removeLabelFromPrintList($id){
        $label = Label::find($id);
        $label->prepare2print = 0;
        $label->save();
        if(Input::has('list')){
            return redirect($_SERVER['HTTP_REFERER']);
        }


        $product = Ex_product::where('sku',$label->code)->first();
        $special = Ex_speceal::where('product_id', $product->product_id)->first();
        return view('label.labelTemplate',compact('label','product','special'));
    }

    public function editLabel2($id){
        $label = Label::find($id);
        $product = Ex_product::where('sku',$label->code)->first();
        $special = Ex_speceal::where('product_id', $product->product_id)->first();
        return view('label.labelTemplate',compact('label','product','special'));
    }

    public function cleanLabelList(Request $request){
        $this->validate($request,[
            'labels'=>'required'
        ]);
        $labels = \GuzzleHttp\json_decode($request->input('labels'),true);
        foreach ($labels as $id){
            $label = Label::find($id);
            $label->prepare2print = 0;
            $label->save();
        }
        return redirect('labelList');
    }

    public function addProductinLabel($id){
        $categorySpecific = Ex_category::find($id);
        $products = $categorySpecific->products()->where('status',1)->where('quantity','>',0)->get();
        foreach ($products as $product){
            $label = Label::where('code',$product->sku)->first();
            if(!isset($label)){
                $ex_description = $product->description;

                $label = new Label();
                $label->code = $product->sku;
                $label->description = $ex_description->name;
                $label->price = round($product->price*1.15,2);
                $label->prepare2print = 1;
                $label->save();
            }
        }
        return redirect($_SERVER['HTTP_REFERER']);
    }
}
