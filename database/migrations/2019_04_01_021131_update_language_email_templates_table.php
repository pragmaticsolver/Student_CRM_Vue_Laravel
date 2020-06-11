<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLanguageEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_templates', function(Blueprint $table) {
            $table->string('subject_ja')->nullable();
            $table->text('content_ja')->nullable();
            $table->tinyInteger('enable')->nullable();
            $table->renameColumn('content', 'content_en');
            $table->renameColumn('subject', 'subject_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_templates', function(Blueprint $table) {
            $table->dropColumn(['subject_ja', 'content_ja', 'enable']);
            $table->renameColumn('content_en', 'content');
            $table->renameColumn('subject_en', 'subject');
        });
    }
}
