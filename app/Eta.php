<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Eta extends Model
{
    protected $table = 'eta';
    protected $fillable = array('model','available_time');

    public function setAvailableTimeAttribute($value)
    {
        $date = Carbon::parse($value);
        $this->attributes['available_time'] = $date->format('d-m-Y');
    }
}
