<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonsToken extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    protected $table="salons_token";
}
