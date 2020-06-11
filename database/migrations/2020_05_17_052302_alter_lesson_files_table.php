<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLessonFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("insert into lesson_files Select 0,id,5,video,now(),video from lessons where video_type='link' and video is not null");
        \DB::statement("insert into lesson_files Select 0,id,1,video,now(),'' from lessons where video_type='file' and video is not null");
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['video_type','video']);
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
            $table->string('video')->nullable();
            $table->enum('video_type',['link', 'file'])->nullable();
        });
      }
}
