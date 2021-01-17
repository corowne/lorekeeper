<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRewardStatPrompt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        schema::table('prompts', function (Blueprint $table) {
            $table->string('user_exp')->nullable()->default(null);
            $table->string('user_points')->nullable()->default(null);
            $table->string('chara_exp')->nullable()->default(null);
            $table->string('chara_points')->nullable()->default(null);
            $table->integer('level_req')->unsigned()->nullable()->default(null);
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
        schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn('user_exp');
            $table->dropColumn('user_points');
            $table->dropColumn('chara_exp');
            $table->dropColumn('chara_points');
            $table->dropColumn('level_req');
        });
    }
}
