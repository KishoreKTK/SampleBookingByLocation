<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Authenticatable
{
  	use SoftDeletes;
    protected $table="user";
    
    protected $fillable = [
        'name', 'email', 'password','created_at',
    ];
    protected $dates = ['deleted_at'];
}
