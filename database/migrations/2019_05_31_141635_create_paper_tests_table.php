<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->integer('course_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->integer('lesson_id')->unsigned();
            $table->float('total_score');
        });

        Schema::table('paper_tests', function(Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paper_tests', function(Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['lesson_id']);
        });

        Schema::dropIfExists('paper_tests');
    }
}
