<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentsFieldToScheduleLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_lessons', function (Blueprint $table) {
            $table->text('comments')->nullable();
            $table->integer('comment_updated_by')->nullable();
            $table->datetime('comment_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_lessons', function (Blueprint $table) {
            $table->dropColumn('comments');
            $table->dropColumn('comment_updated_by');
            $table->dropColumn('comment_updated_at');
        });
    }
}
