<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
class Admin extends Authenticatable
{
	use SoftDeletes;
    protected $table="admin";
    
    protected $fillable = [
        'name', 'email', 'password','created_at',
    ];
    protected $dates = ['deleted_at'];
}
