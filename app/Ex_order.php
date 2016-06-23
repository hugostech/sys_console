<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_order extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_order';
    protected $primaryKey = 'order_id';

    public $timestamps = false;
    public function historys(){
        return $this->hasMany('App\Ex_order_history','order_id');
    }
    public function items(){
        return $this->hasMany('App\Ex_order_product','order_id');
    }
}
