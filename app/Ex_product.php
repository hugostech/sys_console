<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_product';
    protected $primaryKey = 'product_id';
    protected $fillable = array(
        'model', 'quantity', 'stock_status_id', 'shipping', 'price',
        'tax_class_id', 'weight', 'weight_class_id', 'subtract', 'sort_order', 'status','date_added'
    );
    public $timestamps = false;


    public function special()
    {
        return $this->hasOne('App\Ex_speceal', 'product_id');
    }
}
