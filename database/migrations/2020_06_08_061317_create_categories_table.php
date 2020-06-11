<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $tableNames = config('permission.table_names');
        Schema::create($tableNames['categories'], function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name')->default('web')->change();
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
        $tableNames = config('permission.table_names');
        Schema::dropIfExists($tableNames['categories']);
    }
}
