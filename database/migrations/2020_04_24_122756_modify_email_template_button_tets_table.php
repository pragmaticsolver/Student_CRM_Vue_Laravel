<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyEmailTemplateButtonTetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_template_button_texts', function (Blueprint $table) {
            $table->integer('type')->after('email_template_id')->default(1);
        });

        Schema::rename('email_template_button_texts', 'notification_texts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('notification_texts', 'email_template_button_texts');

        Schema::table('email_template_button_texts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
