<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 26/11/17
 * Time: 3:26 AM
 */

namespace backend;


use App\Ex_product;
use App\Ex_speceal;
use GuzzleHttp\Client;

class ExtremepcProduct
{
    public $product;
    public $client;
    public static function find($id){
        $self = new static($id);
        if ($self->check()){
            return $self;
        }else{
            return false;
        }
    }

    public function __construct($id)
    {
        $product = Ex_product::find($id);
        if (!is_null($product)){

            $this->product = $product;

        }
        $this->client = new Client();


    }

    public function check(){
        return !is_null($this->product);
    }

    public function setPrice($price,$incgst=false){
        if ($this->product->price_lock==0){
            if ($incgst){
                $this->product->price = $price/1.15;
            }else{
                $this->product->price = $price;
            }
            return $this->product->save();
        }
    }

    public function setSpecial($price,$incgst=false,$duedate = null){
        if ($this->product->price_lock==0){
            if ($incgst){
                $price = $price/1.15;
            }
            $special = $this->product->special;
            if(is_null($special)){
                $special = new Ex_speceal();
                $special->product_id = $this->product->product_id;
                $special->customer_group_id = 1;
                $special->priority = 0;
                $special->price = $price;
                $special->save();

            }else{
                $special->price = $price;
                $special->save();
            }
            if (!is_null($duedate)){
                $special->date_end = $duedate;
            }
            if (round($special->price*1.15,2) >= round($this->product->price*1.15,2)){
                $special->delete();
            }

        }

    }

    public function cleanSpecial(){
        if ($this->product->price_lock==0){
            Ex_speceal::where('product_id', $this->product->product_id)->delete();
        }
    }

    public function info(){
        $url = env('SNPORT') . "?action=test&code=".$this->product->model;
        $res = $this->client->get($url);
        if ($res->getReasonPhrase()=='OK'){
            $content = $res->getBody()->getContents();
        }
        $data = [];
        if (str_contains($content, 'Average price inc')){
            foreach (explode('<br>',$content) as $item){
                if (str_contains($item, 'font')) continue;
                $row = explode(':', $item);
                $value = str_replace('$','',$row[1]);
                $value = str_replace(',','',$value);
                $value = floatval(trim($value));
                $key = strtolower(str_replace(' ', '', $row[0]));
                $data[$key] = $value;
            }
        }
        return $data;

    }
}