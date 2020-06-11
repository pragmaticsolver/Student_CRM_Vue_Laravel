<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class ChangeTypeFieldInAssessmentQuestionsToStringField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `assessment_questions` CHANGE `type` `type` VARCHAR(191) NOT NULL;");
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
            \DB::statement("ALTER TABLE `assessment_questions` CHANGE `type` `type` ENUM('rating','option','comment','availability-selection-calender') NOT NULL;");
        } catch (QueryException $e){
            dump($e->getMessage());
        }
    }
}
