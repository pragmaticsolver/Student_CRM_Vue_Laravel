<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodoFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('todo_id');
            $table->string('name');
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::table('todo_files', function($table) {
            $table->foreign('todo_id')->references('id')->on('todos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo_files');
    }
}
