<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOneShotYoteisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('one_shot_yoteis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('guest');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('teacher_id');
            $table->tinyInteger('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('one_shot_yoteis');
    }
}
