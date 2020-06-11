<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchIdFieldToMonthlyPaymentsField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_payments', function (Blueprint $table) {
            $table->unsignedInteger('payment_batch_id')->nullable();
        });

        Schema::table('monthly_payments', function(Blueprint $table) {
            $table->foreign('payment_batch_id')->references('id')->on('payment_batches')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_payments', function(Blueprint $table) {
            $table->dropForeign(['payment_batch_id']);
        });

        Schema::table('monthly_payments', function (Blueprint $table) {
            $table->dropColumn('payment_batch_id');
        });
    }
}
