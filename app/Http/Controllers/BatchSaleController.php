<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class BatchSaleController extends Controller
{
    public function index(){
        return view('batchsale.index');
    }

    public function report(Request $request){

        $request->flash();
        return view('batchsale.index');
    }
}
