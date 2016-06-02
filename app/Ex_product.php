<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_product';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

}
