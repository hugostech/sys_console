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

    public function customer(){
        return $this->belongsTo('App\Ex_customer','customer_id');
    }

    public function shipfee(){
        $items = $this->items;
        $total = $this->total * 1.0;
        foreach($items as $item){
            $total = $total-$item->total-($item->tax*$item->quantity);
        }
        $total = $total/1.15;
        return round($total,2);

    }

    public function status(){
        return $this->hasOne('App\Ex_order_status','order_status_id','order_status_id');
    }
}
