<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomDueDateFieldToTodoAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('todo_accesses', function (Blueprint $table) {
            $table->date('due_date');
            $table->date('custom_due_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todo_accesses', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->dropColumn('custom_due_date');
        });
    }
}
