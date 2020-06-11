<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use PhpParser\Node\Stmt\Catch_;

class RemoveScheduleUnitIdFieldFromScheduledLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{
            Schema::table('schedule_lessons', function (Blueprint $table) {
                $table->dropForeign('schedule_lessons_schedule_unit_id_foreign');
            });
        } catch (QueryException $e){
            dump($e->getMessage());
        }

        Schema::table('schedule_lessons', function (Blueprint $table) {
            $table->dropColumn('schedule_unit_id');
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
            $table->unsignedInteger('schedule_unit_id');
        });


        try{
            Schema::table('schedule_lessons', function(Blueprint $table) {
                $table->foreign('schedule_unit_id')->references('id')->on('schedule_units')->onDelete('cascade');
            });
        } catch (QueryException $e){
            dump($e->getMessage());
        }
    }
}
