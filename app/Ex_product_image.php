<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_image extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_product_image';
    protected $primaryKey = 'product_image_id';

    public $timestamps = false;

    public function product(){
        return $this->belongsTo('App\Ex_product','product_id');
    }
}
