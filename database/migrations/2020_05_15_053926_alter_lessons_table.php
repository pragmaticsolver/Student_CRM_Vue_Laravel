<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `lessons` CHANGE `description` `description` TEXT NULL, CHANGE `objectives` `objectives` TEXT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `lessons` CHANGE `description` `description` TEXT Not NULL, CHANGE `objectives` `objectives` TEXT Not NULL;");
    }
}
