<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_csv extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_product_csv';
    protected $primaryKey = 'product_csv_id';
    public $timestamps = false;
}
