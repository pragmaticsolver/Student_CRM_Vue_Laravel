<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTemplateButtonTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template_button_texts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('email_template_id');
            $table->string('key');
            $table->string('text_en')->nullable();
            $table->string('text_ja')->nullable();
        });

        Schema::table('email_template_button_texts', function(Blueprint $table) {
            $table->foreign('email_template_id')->references('id')->on('email_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_template_button_texts', function(Blueprint $table) {
            $table->dropForeign(['email_template_id']);
        });

        Schema::dropIfExists('email_template_button_texts');
    }
}
