<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeysForLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('character_level_rewards', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
        });
        Schema::table('user_level_rewards', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('character_level_rewards', function (Blueprint $table) {
            $table->foreign('level_id')->references('id')->on('level_characters');
        });
        Schema::table('user_level_rewards', function (Blueprint $table) {
            $table->foreign('level_id')->references('id')->on('level_users');
        });
    }
}
