<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_alias extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_url_alias';
    protected $primaryKey = 'url_alias_id';

    public $timestamps = false;
}
