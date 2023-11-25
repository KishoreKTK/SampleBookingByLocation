<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('salon_id');
            $table->integer('user_id');
            $table->string('rating',255)->nullable();
            $table->text('reviews');
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
        Schema::dropIfExists('salon_reviews');
    }
}
