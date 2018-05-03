<?php

namespace App\Http\Controllers;

use backend\ExtremepcProduct;
use Illuminate\Http\Request;

use App\Http\Requests;

class WeekendController extends Controller
{
    public function index(){

    }

    public function get($id){
        $product = ExtremepcProduct::find($id);
        dd($product->info());
    }


}
