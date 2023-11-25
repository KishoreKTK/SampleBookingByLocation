<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('email',255);
            $table->string('phone',255)->nullable();
            $table->string('image',255)->nullable();
            $table->integer('country_id');
            $table->integer('currency_id');
            $table->date('dob');
            $table->integer('gender_id');
            $table->integer('login_type');
            $table->string('unique_id')->nullable();
            $table->integer('suspend');
            $table->text('address');
            $table->integer('active');
            $table->string('remember_token',255)->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
