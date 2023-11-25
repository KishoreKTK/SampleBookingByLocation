<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',255);
            $table->string('promocode',255);
            $table->string('image',255)->nullable();
            $table->integer('amount_type');
            $table->string('amount',255)->nullable();
            $table->string('min_amount',255)->nullable();
            $table->string('max_discount',255)->nullable();
            $table->string('start_date',255)->nullable();
            $table->string('end_date',255)->nullable();
            $table->text('description')->nullable();
            $table->integer('offer_type');
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
        Schema::dropIfExists('offers');
    }
}
