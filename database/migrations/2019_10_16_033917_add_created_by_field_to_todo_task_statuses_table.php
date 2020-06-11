<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByFieldToTodoTaskStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('todo_task_statuses', function (Blueprint $table) {
            $table->tinyInteger('status')->comment('1 - completed, 0 - in complete');
            $table->unsignedInteger('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todo_task_statuses', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('updated_by');
        });
    }
}
