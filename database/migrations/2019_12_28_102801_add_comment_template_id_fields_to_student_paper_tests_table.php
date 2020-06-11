<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentTemplateIdFieldsToStudentPaperTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_paper_tests', function (Blueprint $table) {
            $table->integer('comment_template_id')->nullable();
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_paper_tests', function (Blueprint $table) {
            $table->dropColumn('comment_template_id');
            $table->dropColumn('comment');
        });
    }
}
