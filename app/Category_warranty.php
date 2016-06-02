<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category_warranty extends Model
{
    protected $table = 'category_warranty';

    protected $fillable = array('supplier','detail','tag');
}
