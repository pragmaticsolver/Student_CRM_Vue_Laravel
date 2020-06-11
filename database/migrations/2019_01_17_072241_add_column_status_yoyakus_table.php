<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusYoyakusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yoyakus',function(Blueprint $table){
            $table->tinyInteger('status')->default(0)->comment('0: reserve, 1: attend, 2: cancel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yoyakus',function(Blueprint $table){
            $table->dropColumn('status');
        });
    }
}
