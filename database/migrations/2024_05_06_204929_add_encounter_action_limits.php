<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncounterActionLimits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encounter_prompt_limits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('encounter_prompt_id');
            $table->integer('item_id');
            $table->string('item_type')->default('Item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encounter_prompt_limits');
    }
}
