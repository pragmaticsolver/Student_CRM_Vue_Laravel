<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonExerciseStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_exercise_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_exercise_id');
            $table->unsignedInteger('schedule_id');
            $table->tinyInteger('is_complete');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by');
        });

        Schema::table('lesson_exercise_statuses', function (Blueprint $table) {
            $table->foreign('lesson_exercise_id')->references('id')->on('lesson_exercises')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_exercise_statuses');
    }
}
