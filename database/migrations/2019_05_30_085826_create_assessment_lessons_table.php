<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('lesson_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->integer('assessment_id')->unsigned();
            $table->enum('send_to', ['teacher', 'student']);
        });

        Schema::table('assessment_lessons', function(Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_lessons', function(Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['lesson_id']);
            $table->dropForeign(['assessment_id']);
        });

        Schema::dropIfExists('assessment_lessons');
    }
}
