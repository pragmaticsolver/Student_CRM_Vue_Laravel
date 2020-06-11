<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('downloadable_files');
            $table->dropColumn('pdf_file');
            $table->dropColumn('audio_file');

            $table->text('student_lesson_prep')->nullable();
            $table->text('vocab_list')->nullable();
            $table->text('extra_materials_text')->nullable();
            $table->text('teachers_notes')->nullable();
            $table->text('teachers_prep')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->text('downloadable_files')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('audio_file')->nullable();

            $table->dropColumn('student_lesson_prep');
            $table->dropColumn('vocab_list');
            $table->dropColumn('extra_materials_text');
            $table->dropColumn('teachers_notes');
            $table->dropColumn('teachers_prep');
        });
    }
}
