<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_Neworder_history extends Model
{
    protected $connection = 'new_extremepc_mysql';
    protected $table = 'oc_order_history';
    protected $primaryKey = 'order_history_id';

    public $timestamps = false;

    public function order(){
        return $this->belongsTo('App\Ex_Neworder','order_id');
    }
}
