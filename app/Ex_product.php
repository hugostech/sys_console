<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ex_product extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product';
    protected $primaryKey = 'product_id';
    protected $fillable = array(
        'sku', 'quantity', 'stock_status_id', 'shipping', 'price',
        'tax_class_id', 'weight', 'weight_class_id', 'subtract', 'sort_order', 'status','date_added','mpn', 'model'
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


}
