<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyStudentsTableParentUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('parent_user_id')->nullable()->after('user_id');
        });

        Schema::table('students', function($table) {
            $table->foreign('parent_user_id')->references('id')->on('users')->onDelete('set null');
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
            $table->dropForeign(['parent_user_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('parent_user_id');
        });
    }
}
