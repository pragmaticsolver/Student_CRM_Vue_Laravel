<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysConstrainsSyllabus extends Migration
{
    private $tableConstraints = [
        'yoyakus' => [['customer_id'], ['schedule_id']],
        'students' => [['user_id']],
        'schedules' => [['class_id'], ['teacher_id']],
        'course_schedules' => [['course_id'], ['schedule_id']],
        'units' => [['course_id']],
        'lessons' => [['course_id'], ['unit_id']],
        'schedule_units' => [['schedule_id'], ['unit_id']],
        'schedule_lessons' => [['schedule_id'], ['lesson_id'], ['schedule_unit_id']],
        'tests' => [['lesson_id'], ['course_id'], ['unit_id']],
        'student_tests' => [['student_id'], ['test_id'], ['schedule_id']],
        'questions' => [['test_id']],
        'answers' => [['test_id'], ['question_id']],
        'book_students' => [['book_id'], ['student_id']]
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->_addFKYoyakus();
        $this->_addFKStudents();
        $this->_addFKSchedules();
        $this->_addFKCourseSchedules();
        $this->_addFKUnits();
        $this->_addFKLessons();
        $this->_addFKScheduleUnits();
        $this->_addFKScheduleLessons();
        $this->_addFKTests();
        $this->_addFKStudentTests();
        $this->_addFKQuestions();
        $this->_addFKAnswers();
        $this->_addFKBookStudents();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach($this->tableConstraints as $tableName => $constraints) {
            Schema::table($tableName, function(Blueprint $table) use ($constraints) {
                foreach($constraints as $constraint) {
                    $table->dropForeign($constraint);
                }
            });
        }
    }

    private function _addFKYoyakus()
    {
        Schema::table('yoyakus', function(Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    private function _addFKStudents()
    {
        Schema::table('students', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    private function _addFKSchedules()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    private function _addFKCourseSchedules()
    {
        Schema::table('course_schedules', function(Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    private function _addFKUnits()
    {
        Schema::table('units', function(Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    private function _addFKLessons()
    {
        Schema::table('lessons', function(Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    private function _addFKScheduleUnits()
    {
        Schema::table('schedule_units', function(Blueprint $table) {
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    private function _addFKScheduleLessons()
    {
        Schema::table('schedule_lessons', function(Blueprint $table) {
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('schedule_unit_id')->references('id')->on('schedule_units')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    private function _addFKTests()
    {
        Schema::table('tests', function(Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons');
        });
    }

    private function _addFKStudentTests()
    {
        Schema::table('student_tests', function(Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    private function _addFKQuestions()
    {
        Schema::table('questions', function(Blueprint $table) {
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
        });
    }

    private function _addFKAnswers()
    {
        Schema::table('answers', function(Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
        });
    }

    private function _addFKBookStudents()
    {
        Schema::table('book_students', function(Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }
}
