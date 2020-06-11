<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_usages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->date('month_year');
            $table->date('expiry_month_year')->nullable();
            $table->tinyInteger('is_paid')->default(0);
            $table->integer('yoyaku_id')->nullable();
            $table->date('used_month_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_usages');
    }
}
