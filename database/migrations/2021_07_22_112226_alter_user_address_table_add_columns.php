<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAddressTableAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_address', function (Blueprint $table) {
            $table->enum('addr_title',['1','2','3'])->default('0')->after('user_id');
            $table->string('phone_num',20)->after('last_name');
            $table->enum('same_addr',['0','1'])->default('0')->after('address');
            $table->enum('billing_addr',['0','1'])->default('0')->after('same_addr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
