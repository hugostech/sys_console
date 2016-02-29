<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';

    public function warranty(){
        return $this->belongsTo('App\Warranty','model_id');
    }
}
