<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255);
            $table->string('email',255);
            $table->text('description')->nullable();
            $table->string('location',255)->nullable();
            $table->string('latitude',255)->nullable();
            $table->string('longitude',255)->nullable();
            $table->integer('country_id');
            $table->string('city',255)->nullable();
            $table->string('phone',255)->nullable();
            $table->string('image',255)->nullable();
            $table->integer('currency_id');
            $table->string('sub_title',255)->nullable();
            $table->string('pricing',255)->nullable();
            $table->string('min_price',255)->nullable();
            $table->text('reschedule_policy')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->string('remember_token',255)->nullable();
            $table->string('password');
            $table->integer('active');
            $table->string('logo',255)->nullable();
            $table->integer('suspend');
            $table->integer('featured');
            $table->integer('approved');
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
        Schema::dropIfExists('salons');
    }
}
