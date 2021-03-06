<?php

namespace App\Http\Controllers;

use App\Ex_category;
use backend\ExtremepcProduct;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Ex_speceal;

class CategoryController extends Controller
{
    public function shade(){
        self::selfClearSpecial();
        self::categoryByPrice();
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
//            408=>411,
            1=>410,


            256=>462,
            101=>460,
            103=>458,
            104=>461,
            15=>457,
            109=>463,

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

    private function categoryByPrice(){
        $g1000 = [];
        $b1000500 = [];
        $b500100 = [];
        $b10050 = [];
        $l50 = [];
        foreach (Ex_speceal::all() as $item){
            $price = round($item->price*1.15,2);
            if ($price>=1000){
                $g1000[] = $item->product_id;
            }elseif ($price>=500){
                $b1000500[] = $item->product_id;
            }elseif ($price>=100){
                $b500100[] = $item->product_id;
            }elseif ($price>=50){
                $b10050[] = $item->product_id;
            }elseif ($price>0){
                $l50[] = $item->product_id;
            }
        }
        Ex_category::find(415)->products()->sync($g1000);
        Ex_category::find(314)->products()->sync($b1000500);
        Ex_category::find(315)->products()->sync($b500100);
        Ex_category::find(316)->products()->sync($b10050);
        Ex_category::find(317)->products()->sync($l50);

    }

    /*
    * quantity 0 products delete special price*/
    private function selfClearSpecial(){
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = ExtremepcProduct::find($item->product_id);
            if ($product){
                if($product->product->quantity<1){
                    $product->cleanSpecial();
                }
            }else{
                $item->delete();
            }

        }
    }
}
