<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonCategoriesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_categories_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('approve_id');
            $table->integer('log_id');
            $table->integer('salon_id');
            $table->integer('category_id');
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
        Schema::dropIfExists('salon_categories_log');
    }
}