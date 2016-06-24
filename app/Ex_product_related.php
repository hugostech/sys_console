<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_related extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_product_related';
//    protected $primaryKey = 'product_id';
    public $timestamps = false;
}
