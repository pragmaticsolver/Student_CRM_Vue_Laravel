<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBookStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('books_checkin', 'book_students');

        Schema::table('book_students', function(Blueprint $table) {
            $table->renameColumn('checkout_status', 'status');
            $table->date('checkin_date')->nullable()->change();
            $table->date('expected_checkin_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('book_students', 'books_checkin');

        Schema::table('books_checkin', function(Blueprint $table) {
            $table->renameColumn('status', 'checkout_status');
            $table->dropColumn(['expected_checkin_date']);
        });
    }
}
