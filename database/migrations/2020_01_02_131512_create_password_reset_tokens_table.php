<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->text('token');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try{
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->dropForeign('password_reset_tokens_user_id_foreign');
            });
        } catch (QueryException $e){
            dump($e->getMessage());
        }

        Schema::dropIfExists('password_reset_tokens');
    }
}
