<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_store extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_product_to_store';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

}
