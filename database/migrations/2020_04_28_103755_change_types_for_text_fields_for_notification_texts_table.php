<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypesForTextFieldsForNotificationTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_texts', function (Blueprint $table) {
            $table->text('text_en')->nullable()->change();
            $table->text('text_ja')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_texts', function (Blueprint $table) {
            $table->string('text_en')->nullable()->change();
            $table->string('text_ja')->nullable()->change();
        });
    }
}
