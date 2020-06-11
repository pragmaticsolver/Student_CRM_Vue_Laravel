<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->enum('type', ['rating', 'option', 'comment']);
            $table->integer('assessment_id')->unsigned();
            $table->text('option_values')->nullable();
        });

        Schema::table('assessment_questions', function (Blueprint $table) {
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
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
        });

        Schema::dropIfExists('assessment_questions');
    }
}
