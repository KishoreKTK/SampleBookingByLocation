<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonServicesDraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_services_draft', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('approve_id');
            $table->integer('salon_id');
            $table->integer('category_id');
            $table->string('service',255);
            $table->string('time',255);
            $table->string('amount',255);
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
        Schema::dropIfExists('salon_services_draft');
    }
}
