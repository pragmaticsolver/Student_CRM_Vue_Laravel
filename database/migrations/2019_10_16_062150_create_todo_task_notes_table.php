<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodoTaskNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_task_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('todo_access_id');
            $table->unsignedInteger('todo_task_id');
            $table->text('note_text')->nullable();
            $table->timestamps();
            $table->unsignedInteger('updated_by');
        });

        Schema::table('todo_task_notes', function($table) {
            $table->foreign('todo_access_id')->references('id')->on('todo_accesses')->onDelete('cascade');
            $table->foreign('todo_task_id')->references('id')->on('todo_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo_task_notes');
    }
}
