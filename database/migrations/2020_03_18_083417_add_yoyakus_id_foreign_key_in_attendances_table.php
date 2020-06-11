<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYoyakusIdForeignKeyInAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedInteger('yoyaku_id')->change();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('yoyaku_id')->references('id')->on('yoyakus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['yoyaku_id']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('yoyaku_id')->change();
        });
    }
}
