<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_speceal extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_product_special';
    protected $primaryKey = 'product_special_id';
    public $timestamps = false;

    public function product(){
        return $this->belongsTo('App\Ex_product','product_id');
    }
}
