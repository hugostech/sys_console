<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_order_history extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_order_history';
    protected $primaryKey = 'order_history_id';

    public $timestamps = false;

    public function order(){
        return $this->belongsTo('App\Ex_order','order_id');
    }

    public function status(){
        return $this->belongsTo(Ex_order_status::class, 'order_status_id', 'order_status_id');
    }
}
