<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRequiredColStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('firstname_kanji')->nullable()->change();
            $table->string('lastname_kanji')->nullable()->change();
            $table->string('firstname_furigana')->nullable()->change();
            $table->string('lastname_furigana')->nullable()->change();
            $table->string('mobile_phone')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('toiawase_referral')->nullable()->change();
            $table->string('toiawase_memo')->nullable()->change();
            $table->string('toiawase_getter')->nullable()->change();
            $table->string('toiawase_houhou')->nullable()->change();
            $table->string('teacher_id')->nullable()->change();
            $table->string('comment')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('firstname_kanji')->nullable(false)->change();
            $table->string('lastname_kanji')->nullable(false)->change();
            $table->string('firstname_furigana')->nullable(false)->change();
            $table->string('lastname_furigana')->nullable(false)->change();
            $table->string('mobile_phone')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('toiawase_referral')->nullable(false)->change();
            $table->string('toiawase_memo')->nullable(false)->change();
            $table->string('toiawase_getter')->nullable(false)->change();
            $table->string('toiawase_houhou')->nullable(false)->change();
            $table->string('teacher_id')->nullable(false)->change();
            $table->string('comment')->nullable(false)->change();
        });
    }
}
