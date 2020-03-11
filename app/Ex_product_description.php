<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_description extends Model
{

    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product_description';
    protected $primaryKey = 'product_id';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'language_id',
        'name',
        'description',
        'meta_title',
    ];


}
