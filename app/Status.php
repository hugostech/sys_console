<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';

    public function warranty(){
        return $this->belongsTo('App\Warranty','model_id');
    }
}
