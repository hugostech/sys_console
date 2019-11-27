<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_attribute extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product_attribute';

    public $timestamps = false;
}
