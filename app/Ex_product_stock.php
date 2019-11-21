<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_stock extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product_stock';
    protected $primaryKey = 'product_id';
    protected $fillable = array(
        'branch_akl', 'warning_akl', 'branch_wlg', 'warning_wlg', 'supplier', 'product_id'
    );
    public $timestamps = false;

}
