<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('capital',255);
            $table->string('citizenship',255);
            $table->string('country_code',255);
            $table->string('currency',255);
            $table->string('currency_code',255);
            $table->string('currency_sub_unit',255);
            $table->string('currency_symbol',255);
            $table->integer('currency_decimals');
            $table->string('full_name',255);
            $table->string('iso_3166_2',255);
            $table->string('iso_3166_3',255);
            $table->string('name',255);
            $table->string('region_code',255);
            $table->string('sub_region_code',255);
            $table->integer('eea');
            $table->string('calling_code',255);
            $table->string('flag',255);
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
        Schema::dropIfExists('countries');
    }
}
