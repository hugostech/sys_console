<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_stock_status extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_stock_status';
    protected $primaryKey = 'stock_status_id';
    public $timestamps = false;

    public function products(){
        return $this->hasMany('App\Ex_product','stock_status_id');
    }

}
