<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingHoursTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_hours_time', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('salon_id');
            $table->string('sunday_start',255)->nullable();
            $table->string('sunday_end',255)->nullable();
            $table->string('monday_start',255)->nullable();
            $table->string('monday_end',255)->nullable(); 
            $table->string('tuesday_start',255)->nullable();
            $table->string('tuesday_end',255)->nullable(); 
            $table->string('wednesday_start',255)->nullable();
            $table->string('wednesday_end',255)->nullable(); 
            $table->string('thursday_start',255)->nullable();
            $table->string('thursday_end',255)->nullable(); 
            $table->string('friday_start',255)->nullable();
            $table->string('friday_end',255)->nullable(); 
            $table->string('saturday_start',255)->nullable();
            $table->string('saturday_end',255)->nullable(); 
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
        Schema::dropIfExists('working_hours_time');
    }
}
