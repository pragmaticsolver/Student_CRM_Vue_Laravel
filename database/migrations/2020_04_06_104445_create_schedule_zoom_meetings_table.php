<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleZoomMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_zoom_meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('schedule_id');
            $table->date('date');
            $table->bigInteger('zoom_meeting_id');
        });

        Schema::table('schedule_zoom_meetings', function(Blueprint $table) {
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('restrict');
            $table->foreign('zoom_meeting_id')->references('id')->on('zoom_meetings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_zoom_meetings');
    }
}
