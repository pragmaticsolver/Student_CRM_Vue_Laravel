<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('firstname_kanji');
            $table->string('lastname_kanji');
            $table->string('firstname_furigana');
            $table->string('lastname_furigana');
            $table->tinyInteger('status')->default(0);
            $table->date('join_date')->nullable();
            $table->string('home_phone');
            $table->string('mobile_phone');
            $table->string('email');
            $table->string('address');
            $table->text('toiawase_referral');
            $table->text('toiawase_memo');
            $table->text('toiawase_getter');
            $table->text('toiawase_houhou');
            $table->date('toiawase_date')->nullable();
            $table->integer('teacher_id');
            $table->date('birthday')->nullable();
            $table->text('comment');
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
