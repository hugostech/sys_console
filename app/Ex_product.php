<?php

namespace App;

use backend\ExtremepcProduct;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Model;

class Ex_product extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product';
    protected $primaryKey = 'product_id';
    protected $fillable = array(
        'sku', 'quantity', 'stock_status_id', 'shipping', 'price',
        'tax_class_id', 'weight', 'weight_class_id', 'subtract', 'sort_order',
        'status','date_added','mpn', 'model', 'ean', 'jan', 'upc'
    );
    public $timestamps = false;


    public function special()
    {
        return $this->hasOne('App\Ex_speceal', 'product_id');
    }

    public function categorys(){
        return $this->belongsToMany('App\Ex_category','oc_product_to_category','product_id','category_id');
    }

    public function relates(){
        return $this->belongsToMany('App\Ex_product','oc_product_related','product_id','related_id');
    }

    public function master(){
        return $this->belongsToMany('App\Ex_product','oc_product_related','related_id','product_id');
    }

    public function stock_status(){
        return $this->belongsTo('App\Ex_stock_status','stock_status_id');
    }


    public function images(){
        return $this->hasMany('App\Ex_product_image','product_id');
    }

    public function description(){
        return $this->hasOne('App\Ex_product_description','product_id');
    }

    public function csvs(){
        return $this->hasMany('App\Ex_product_csv','product_id');
    }

    public function stock(){
        return $this->hasOne(Ex_product_stock::class, 'product_id', 'product_id');
    }

    public function store(){
        return $this->hasMany(Ex_product_store::class, 'product_id', 'product_id');
    }

    public function brand(){
        return $this->belongsTo(Ex_product_brand::class, 'manufacturer_id', 'manufacturer_id');
    }

    public function scopeRocLinked($query){
        $query->whereNotNull('sku');
    }

    public function pushPriceToRoc(){
        if (trim($this->sku)){
            $client = new Client();
            $url = config('app.roctech_admin')."/snyc_price_roc.aspx?pid={$this->sku}&price={$this->price}&sp=".($this->special?$this->special->price:0);
            $res = $client->request('GET', $url);
            return $res->getBody()->getContents();

        }else{
            return false;
        }

    }

    public function setDiscountRatio($ratio, $margin_rate=0,$base_price_changable=false){
        $product = ExtremepcProduct::find($this->product_id);
        $info = $product->info();
        if (isset($info['averagecost']) && $info['averagecost']>0){
            $bottom_price = $info['averagecost'] * (1+$margin_rate);
            $current_base_price = $product->getBasePrice();
            $current_special_price = $product->getSpecial();
            $final_base_price =$current_base_price;
            $expected_special = $current_base_price*(1-$ratio);
            if ($current_special_price>0){
                $final_special = $current_special_price<$expected_special?$current_special_price:($bottom_price<$expected_special?$expected_special:$bottom_price);
            }else{
                $final_special = $bottom_price<$expected_special?$expected_special:$bottom_price;
            }
            $final_special = $this->prettyPrice($final_special, false);

            $tem_ratio = 1-$final_special/$current_base_price;
            if ($tem_ratio<0){
                throw new \Exception('special price greater than base price error');
            }

            if ($tem_ratio<$ratio && $base_price_changable){
                $final_base_price = $this->prettyPrice($final_special/(1-$ratio));
            }
            $product->setPrice($final_base_price, true);
            $product->setSpecial($final_special, true);
            return compact('final_special', 'final_base_price');



        }else{
            return false;
        }
    }

    private function prettyPrice($price, $nineLeaf=true){
        if ($nineLeaf){
            $real = intval($price*1.15/10)*10+9;
        }else{
            $real = floor($price*1.15);
        }
        return $real/1.15;
    }


}
