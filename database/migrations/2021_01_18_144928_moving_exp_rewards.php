<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MovingExpRewards extends Migration
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
            $table->dropColumn('user_exp');
            $table->dropColumn('user_points');
            $table->dropColumn('chara_exp');
            $table->dropColumn('chara_points');
        });

        Schema::create('prompt_exp_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('prompt_id')->unsigned()->default(0);
            $table->string('user_exp')->nullable()->default(null);
            $table->string('user_points')->nullable()->default(null);
            $table->string('chara_exp')->nullable()->default(null);
            $table->string('chara_points')->nullable()->default(null);

            $table->foreign('prompt_id')->references('id')->on('prompts');
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
            $table->string('user_exp')->nullable()->default(null);
            $table->string('user_points')->nullable()->default(null);
            $table->string('chara_exp')->nullable()->default(null);
            $table->string('chara_points')->nullable()->default(null);
        });

        Schema::dropIfExists('prompt_exp_rewards');
    }
}
