<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonCategories extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    protected $table="salon_categories";
}
