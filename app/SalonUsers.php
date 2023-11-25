<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;


class SalonUsers extends Authenticatable
{
     use SoftDeletes;
    protected $table="salon_users";
    
    protected $fillable = [
        'name', 'email', 'password','created_at',
    ];
    protected $dates = ['deleted_at'];
}
