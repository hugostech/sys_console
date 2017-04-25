<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kill_price_product extends Model
{
    protected $table = 'kill_price_products';
    protected $fillable = array(
        'model','product_id','url','bottomPrice','target','note'
    );
}
