<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class ChangeTypeEnumInAssessmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `assessment_questions` CHANGE `type` `type` ENUM('rating','option','comment','availability-selection-calender') NOT NULL;");

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->unsignedInteger('availability_selection_calendar_id')->nullable();
        });

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->foreign('availability_selection_calendar_id')->references('id')->on('availability_selection_calendars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Data truncate error
        try{
            \DB::statement("ALTER TABLE `assessment_questions` CHANGE `type` `type` ENUM('rating','option','comment') NOT NULL;");
        } catch (QueryException $e){
            dump($e->getMessage());
        }
        
        try{
            Schema::table('assessment_questions', function (Blueprint $table) {
                $table->dropForeign('assessment_questions_availability_selection_calendar_id_foreign');
            });
        } catch (QueryException $e){
            dump($e->getMessage());
        }

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn('availability_selection_calendar_id');
        });
    }
}
