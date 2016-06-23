<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_order_product extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_order_product';
    protected $primaryKey = 'order_product_id';

    public $timestamps = false;

    public function order(){
        return $this->belongsTo('App\Ex_order','order_id');
    }
}
