<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id');
            $table->integer('section')->comment('1 - Downloadable files, 2 - Pdf files, 3 - Audio files');
            $table->text('file_path');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
        });

        Schema::table('lesson_files', function($table) {
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_files');
    }
}
