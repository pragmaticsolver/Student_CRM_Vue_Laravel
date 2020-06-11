<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentCategoryFieldToMonthlyPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_payments', function (Blueprint $table) {
            $table->string('payment_category')->nullable()->after('number_of_lessons');
            $table->string('period')->nullable()->change();
            $table->integer('number_of_lessons')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_payments', function (Blueprint $table) {
            $table->dropColumn('payment_category');
            $table->string('period')->nullable(false)->change();
            $table->integer('number_of_lessons')->nullable(false)->change();
        });
    }
}
