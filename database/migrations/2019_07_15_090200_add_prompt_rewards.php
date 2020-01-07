<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromptRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('prompt_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('prompt_id')->unsigned()->default(0);
            $table->string('rewardable_type');
            $table->integer('rewardable_id')->unsigned();
            $table->integer('quantity')->unsigned();
            
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
        Schema::dropIfExists('prompt_rewards');
    }
}
