<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::table('scores', function (Blueprint $table) {
            // this is only if the foreign exists so if the rollback was ran for this,
            // delete this line before re-running migrate
            $table->dropForeign('scores_user_id_foreign');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('family_user', function (Blueprint $table) {
            // same here
            $table->dropForeign('family_user_user_id_foreign');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('family_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
