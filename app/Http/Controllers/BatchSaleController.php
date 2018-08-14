<?php

namespace App\Http\Controllers;

use App\Ex_product;
use backend\ExtremepcProduct;
use Illuminate\Http\Request;

use App\Http\Requests;

class BatchSaleController extends Controller
{
    public function index(){
        return view('batchsale.index');
    }

    public function report(Request $request){
        $target_percentage = $request->get('target_percentage')/100;
        $pretty_price = $request->get('pretty_price');
        $margin_rate = $request->get('margin_rate')/100;
        $base_changeable = $request->get('base_changeable');
        $test = $request->get('test');

        if ($request->get('with_stock') == 1){
            $products = Ex_product::where('quantity','>',0)->pluck('product_id');
        }else{
            $products = Ex_product::pluck('product_id');
        }
        foreach ($products as $product_id){
            $product = ExtremepcProduct::find($product_id);
            $this->updateProduct($product, $target_percentage, $base_changeable, $margin_rate, $pretty_price, $test);

        }
        $request->flash();
        $request->session()->flash('status', 'success');
        return view('batchsale.index');
    }

    public function updateProduct(ExtremepcProduct $product, $target_percentage, $base_changeable, $margin_rate, $pretty_price, $test=1){
        if (!is_numeric($product->product->model)){
            return;
        }
        $special = $product->getSpecial();
        $base = $product->getBasePrice();
        $init_base = [$base, $special];
        $info = $product->info();
        if (count($info)<1){
            return;
        }
        if (empty($info['averagecost'])){
            return;
        }
        $bottom_cost = $info['averagecost']*(1+$margin_rate);

        $p = 1 - $special/$base;
        if ($p==1 || $p<$target_percentage){
            $tem_special = $base*(1-$target_percentage);
            if (($tem_special<$special && $tem_special>=$bottom_cost) || $special==0){
                $special = $tem_special;
            }else{
                $special = $bottom_cost>$special?$special:$bottom_cost;
            }

            $special = $this->prettyPirce($special, $pretty_price);
            $p = 1 - $special/$base;
            if ($p < $target_percentage){
                if ($base_changeable==1){
                    $base = $special/(1-$target_percentage);
                    $base = $this->prettyPirce($base, $pretty_price);
                }
            }
            if ($test == 1){
                $final = [$base, $special];
                $model = $product->product->model;
                dd(compact('model','target_percentage','base_changeable','margin_rate','pretty_price','init_base','final'));
            }else{
                $product->setPrice($base);
                $product->setSpecial($special);
            }



        }
    }

    private function prettyPirce($price, $pretty){
        if ($pretty == 1){
            return ceil($price*1.15)/1.15;
        }else{
            return $price;
        }

    }

    public function check(){
        $products = Ex_product::where('quantity','>',0)->pluck('product_id');
        $list = [];
        foreach ($products as $id){
            $product = ExtremepcProduct::find($id);
            $model = $product->product->model;
            if (is_numeric($model)){
                $price = $product->getSpecial();
                $base = $product->getBasePrice();
                $cost = $product->info()['averagecost'];
                if ($price != 0 && $cost < $price){
                    $list[] = compact('model','base','price','cost');
                }elseif($price == 0 ){
                    $list[] = compact('model','base','price','cost');
                }elseif (empty($cost)){
                    $list[] = compact('model','base','price','cost');
                }

            }

        }
        return view('batchsale.report',compact('list'));
    }
}
