<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLootTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loot_tables', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('display_name');
        });

        // I know this doesn't pluralise this way but 
        Schema::create('loots', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('loot_table_id')->unsigned();
            $table->string('rewardable_type');
            $table->integer('rewardable_id')->unsigned();
            
            $table->integer('quantity')->unsigned();
            $table->integer('weight')->unsigned();
            
            $table->foreign('loot_table_id')->references('id')->on('loot_tables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loots');
        Schema::dropIfExists('loot_tables');
    }
}
