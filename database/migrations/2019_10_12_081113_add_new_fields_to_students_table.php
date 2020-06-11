<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('office_name')->nullable();
            $table->string('office_address')->nullable();
            $table->string('office_phone')->nullable();

            $table->string('school_name')->nullable();
            $table->string('school_address')->nullable();
            $table->string('school_phone')->nullable();
            
            $table->string('guardian1_name')->nullable();
            $table->string('guardian1_address')->nullable();
            $table->string('guardian1_phone')->nullable();

            $table->string('guardian2_name')->nullable();
            $table->string('guardian2_address')->nullable();
            $table->string('guardian2_phone')->nullable();
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
            $table->dropColumn('office_name');
            $table->dropColumn('office_address');
            $table->dropColumn('office_phone');

            $table->dropColumn('school_name');
            $table->dropColumn('school_address');
            $table->dropColumn('school_phone');

            $table->dropColumn('guardian1_name');
            $table->dropColumn('guardian1_address');
            $table->dropColumn('guardian1_phone');

            $table->dropColumn('guardian2_name');
            $table->dropColumn('guardian2_address');
            $table->dropColumn('guardian2_phone');
        });
    }
}
