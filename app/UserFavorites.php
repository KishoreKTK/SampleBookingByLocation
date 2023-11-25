<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFavorites extends Model
{
       use SoftDeletes;
	protected $dates = ['deleted_at'];
    protected $table="user_favorites";
}
