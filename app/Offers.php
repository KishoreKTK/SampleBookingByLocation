<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offers extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    protected $table="offers";
}
