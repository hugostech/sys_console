<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_brand extends Model
{

    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_manufacturer';
    protected $primaryKey = 'manufacturer_id';
    public $timestamps = false;



}
