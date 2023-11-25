<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceOffersLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_offers_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('audit_id');
            $table->integer('salon_id');
            $table->integer('service_id');
            $table->integer('offer_id');
            $table->string('discount_price',255);
            $table->string('start_date',255);
            $table->string('end_date',255);
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
        Schema::dropIfExists('service_offers_log');
    }
}
