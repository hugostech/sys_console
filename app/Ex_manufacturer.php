<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_manufacturer extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_manufacturer';
    protected $primaryKey = 'manufacturer_id';

    public $timestamps = false;
}
