<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForStudentFieldToAssessmentUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_users', function (Blueprint $table) {
            $table->unsignedInteger('for_student')->nullable()->after('user_id')->comment('student who is being assessed (by teacher)');
            $table->unsignedInteger('user_id')->comment('user who is taking an assessment')->change();
        });

        Schema::table('assessment_users', function (Blueprint $table) {
            $table->foreign('for_student')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try{
            Schema::table('assessment_users', function (Blueprint $table) {
                $table->dropForeign('assessment_users_for_student_foreign');
            });
        } catch (QueryException $e){
            dump($e->getMessage());
        }

        Schema::table('assessment_users', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->comment('')->change();
            $table->dropColumn('for_student');
        });
    }
}
