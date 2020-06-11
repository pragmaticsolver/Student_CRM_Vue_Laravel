<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentStatusAndOtherFieldsToMonthlyPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_payments', function (Blueprint $table) {
            $table->string('status');
            $table->string('stripe_invoice_id')->nullable();
            $table->string('stripe_invoice_url')->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
            $table->dropColumn('status');
            $table->dropColumn('stripe_invoice_id');
            $table->dropColumn('stripe_invoice_url');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
