<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class adminController extends Controller
{
    public function showSupplier(){
        return view('addSupplier');
    }

    public function storeSupplier(Request $request){

        $supplier = new Supplier();
        $supplier->name = $request->input('name');
        $supplier->type = $request->input('type');
        $supplier->save();

        if($request->input('type')=='doc'){
            if($request->hasFile('doc')){
                $filePath = $supplier->id.$supplier->name.'.'.$request->file('doc')->getClientOriginalExtension();
                $request->file('doc')->move(public_path('suplierDoc'),$filePath);
                $supplier->doc = url('/',['suplierDoc',$filePath]);
                $supplier->save();
            }
        }else{
            $supplier->doc = $request->input('url');
            $supplier->save();
        }
        return redirect('supplier');


    }

    public function listSuppliers(){
	$usingSuppliers = Supplier::where('disable','n')->get();
        $disableSuppliers = Supplier::where('disable','y')->get();
        return view('listSuppliers',compact('usingSuppliers','disableSuppliers'));
    }


    public function editSupplier($id){
        $supplier = Supplier::find($id);
        return view('editSupplier',compact('supplier'));
    }

    public function updateSupplier($id,Request $request){
        $supplier = Supplier::find($id);
        $supplier->name = $request->input('name');
        $supplier->type = $request->input('type');
        if($request->input('disable')=='y'){
            $supplier->disable = $request->input('disable');
        }
	$supplier->save();
        if($request->input('type')=='doc'){
            if($request->hasFile('doc')){
                $filePath = $supplier->id.$supplier->name.'.'.$request->file('doc')->getClientOriginalExtension();
                $request->file('doc')->move(public_path('suplierDoc'),$filePath);
                $supplier->doc = url('/',['suplierDoc',$filePath]);
                $supplier->save();
            }
        }else{
            $supplier->doc = $request->input('url');
            $supplier->save();
        }
        return redirect('suppliers');
    }
}
