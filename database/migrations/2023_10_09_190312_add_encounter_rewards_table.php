<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncounterRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encounter_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('encounter_id')->unsigned()->default(0);
            $table->string('rewardable_type');
            $table->integer('rewardable_id')->unsigned();
            $table->integer('quantity')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encounter_rewards');
    }
}
