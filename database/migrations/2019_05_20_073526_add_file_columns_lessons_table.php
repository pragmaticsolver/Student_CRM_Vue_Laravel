<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileColumnsLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function(Blueprint $table) {
            $table->text('downloadable_files')->nullable();
            $table->string('pdf_file', 191)->nullable();
            $table->string('audio_file', 191)->nullable();
            $table->enum('video_type', ['link', 'file'])->nullable();
            $table->string('video')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function(Blueprint $table) {
            $table->dropColumn(['downloadable_files', 'pdf_file', 'audio_file', 'video']);
        });
    }
}
