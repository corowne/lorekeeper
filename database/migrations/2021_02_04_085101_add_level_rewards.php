<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_level_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('level_id')->unsigned()->default(0);
            $table->string('rewardable_type');
            $table->integer('rewardable_id')->unsigned();
            $table->integer('quantity')->unsigned();
            
            $table->foreign('level_id')->references('id')->on('level_users');
        });

        Schema::create('character_level_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('level_id')->unsigned()->default(0);
            $table->string('rewardable_type');
            $table->integer('rewardable_id')->unsigned();
            $table->integer('quantity')->unsigned();
            
            $table->foreign('level_id')->references('id')->on('level_characters');
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
        Schema::dropIfExists('user_level_rewards');
        Schema::dropIfExists('character_level_rewards');
    }
}
