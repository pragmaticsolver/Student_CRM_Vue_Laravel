<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassUsageSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_usage_summaries', function (Blueprint $table) {
            $table->unsignedInteger('customer_id');
            $table->date('month_year');
            $table->integer('paid')->nullable();
            $table->integer('unpaid')->nullable();
            $table->integer('used')->nullable();
            $table->integer('used_leftovers')->nullable();
            $table->integer('new_leftovers')->nullable();
            $table->integer('leftovers')->nullable();
            $table->integer('expiring')->nullable();
            $table->integer('cancelled')->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::table('class_usage_summaries', function($table) {
            $table->primary(['customer_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_usage_summaries');
    }
}
