<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReviewOption extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    protected $table="user_review_options";
}
