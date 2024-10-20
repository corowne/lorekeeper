<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharacterStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // only characters have stats
        Schema::create('stats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('abbreviation');
            $table->integer('default')->unsigned();
            // step increase so it doesnt have to increase by one
            $table->integer('step')->unsigned()->nullable();
            // multiplier that affects step (aka (current + step) X multiplier or just current x multiplier)
            $table->string('multiplier')->nullable();
        });

        //user levels
        Schema::create('level_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('level')->default(1);
            $table->integer('exp_required')->unsigned();
            $table->integer('stat_points')->unsigned();
        });

        // stats assigned to character
        Schema::create('character_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('character_id')->unsigned();
            $table->integer('stat_id')->unsigned();
            $table->integer('stat_level')->unsigned()->default(1);
            // 
            $table->integer('count')->unsigned();
            // for stats like health
            $table->integer('current_count')->unsigned()->nullable();

            $table->foreign('character_id')->references('id')->on('characters');
            $table->foreign('stat_id')->references('id')->on('stats');
        });

        // character levels
        Schema::create('character_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('character_id')->unsigned();
            $table->integer('current_level')->unsigned()->default(1);
            $table->integer('current_exp')->unsigned()->default(0);
            $table->integer('current_points')->unsigned()->default(0);

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
        Schema::dropIfExists('character_stats');
        Schema::dropIfExists('character_levels');
        Schema::dropIfExists('stats');
        Schema::dropIfExists('level_users');
    }
}
