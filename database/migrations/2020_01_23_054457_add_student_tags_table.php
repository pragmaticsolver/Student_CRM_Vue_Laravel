<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class AddStudentTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('tag_id');
        });

        Schema::table('student_tags', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         try{
            Schema::table('student_tags', function (Blueprint $table) {
                $table->dropForeign('student_tags_student_id_foreign');
                $table->dropForeign('student_tags_tag_id_foreign');
            });
        } catch (QueryException $e){
            dump($e->getMessage());
        }
        Schema::dropIfExists('student_tags');
    }
}
