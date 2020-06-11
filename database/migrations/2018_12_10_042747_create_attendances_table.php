<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('yoyaku_id');
            $table->integer('teacher_id');
            $table->integer('payment_plan_id');
            $table->integer('class_id');
            $table->integer('schedule_id');
            $table->tinyInteger('status')->default(0);
            $table->integer('partial_cancel')->default(0);
            $table->integer('full_cancel')->default(0);
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
