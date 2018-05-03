<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRecord extends Model
{
    protected $table = 'product_records';
    protected $fillable = [
        'product_id','model','price','special'
    ];
}
