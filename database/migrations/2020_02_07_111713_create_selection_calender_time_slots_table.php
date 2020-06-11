<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class CreateSelectionCalenderTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_calender_time_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('calendar_id');
            $table->tinyInteger('day_of_week');
            $table->time('from');
            $table->time('to');
        });

        Schema::table('selection_calender_time_slots', function (Blueprint $table) {
            $table->foreign('calendar_id')->references('id')->on('availability_selection_calendars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try{
            Schema::table('selection_calender_time_slots', function (Blueprint $table) {
                $table->dropForeign('selection_calender_time_slots_calendar_id_foreign');
            });
        } catch (QueryException $e){
            dump($e->getMessage());
        }
        Schema::dropIfExists('selection_calender_time_slots');
    }
}
