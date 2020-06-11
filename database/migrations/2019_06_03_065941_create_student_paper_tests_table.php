<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentPaperTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_paper_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->integer('paper_test_id')->unsigned();
            $table->integer('schedule_id')->unsigned();
            $table->float('score')->unsigned();
            $table->float('total_score')->unsigned();
            $table->date('date');
            $table->text('comment_en')->nullable();
            $table->text('comment_ja')->nullable();
        });

        Schema::table('student_paper_tests', function(Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('paper_test_id')->references('id')->on('paper_tests')->onDelete('cascade');
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
        Schema::table('student_paper_tests', function(Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['paper_test_id']);
            $table->dropForeign(['schedule_id']);
        });

        Schema::dropIfExists('student_paper_tests');
    }
}
