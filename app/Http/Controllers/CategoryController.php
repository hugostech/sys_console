<?php

namespace App\Http\Controllers;

use App\Ex_category;
use Illuminate\Http\Request;

use App\Http\Requests;

class CategoryController extends Controller
{
    public function shade(){
        $map = [
            384=>413,
            387=>414,
            100=>412,
            283=>350,
            282=>351,
            3=>352,
            13=>353,
            12=>354,
            14=>355,
            4=>409,
            408=>411
        ];
        foreach ($map as $r=>$s){
            Ex_category::find($s)->products()->sync($this->onSaleProduct($r));
        }
        return redirect()->back();

    }

    private function onSaleProduct($categoryid){
        $products = [];
        $category = Ex_category::find($categoryid);
        foreach ($category->products as $product){
            if (!is_null($product->special)){
                $products[] = $product->product_id;
            }
        }
        return $products;
    }
}
