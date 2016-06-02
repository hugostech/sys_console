<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Old_client extends Model
{
	protected $connection = 'old_extremepc_mysql';
	protected $table = 'customers';
}

