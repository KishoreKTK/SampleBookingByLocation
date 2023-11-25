<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHours extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    protected $table="working_hours_time";
}
