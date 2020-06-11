<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUserChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_children', function (Blueprint $table) {
            Schema::dropIfExists('user_children');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('user_children', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('student_id');
        });

        Schema::table('user_children', function($table) {
            $table->primary(['user_id', 'student_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }
}
