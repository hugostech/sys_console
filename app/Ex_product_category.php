<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_category extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product_to_category';
    protected $primaryKey = 'product_id';
    public $timestamps = false;
}
