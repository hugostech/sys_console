<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_csv extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product_csv';
    public $timestamps = false;
    protected $fillable = array(
        'supplier_code','price','stock','model','supplier', 'product_id'
    );
}
