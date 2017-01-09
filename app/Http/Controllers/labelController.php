<?php

namespace App\Http\Controllers;

use App\Ex_product;
use App\Label;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

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
                $label->description = $ex_description->name;
                $label->price = round(count($product->special)>0?$product->sepcial->price*1.15:$product->price*1.15,2);
                $label->save();

            }else{
                $label->price = round(count($product->special)>0?$product->sepcial->price*1.15:$product->price*1.15,2);
                $label->save();
            }
        }
        return view('label.labelTemplate',compact('label'));
    }

    public function editLabel(Request $request){
        $this->validate($request,[
            'label_id'=>'required'
        ]);
        $label = Label::find($request->input('label_id'));
        $label->update($request->all());
        return view('label.labelTemplate',compact('label'));

    }

    public function addLabel2PrintList($id){
        $label = Label::find($id);
        $label->prepare2print = 1;
        $label->save();
        return view('label.labelTemplate',compact('label'));

    }

    public function labelList(){

        $labels = Label::where('prepare2print',1)->paginate(10);
        if(Input::has('print')){
            return view('label.print',compact('labels'));
        }
        return view('label.labelList',compact('labels'));

    }

    public function removeLabelFromPrintList($id){
        $label = Label::find($id);
        $label->prepare2print = 0;
        $label->save();
        return view('label.labelTemplate',compact('label'));
    }

    public function editLabel2($id){
        $label = Label::find($id);
        return view('label.labelTemplate',compact('label'));
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
}
