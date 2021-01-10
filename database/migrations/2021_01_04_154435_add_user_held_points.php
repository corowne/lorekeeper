<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserHeldPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('current_level')->unsigned()->default(1);
            $table->integer('current_exp')->unsigned()->default(0);
            $table->integer('current_points')->unsigned()->default(0);
        });

        Schema::create('level_characters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('level');
            $table->integer('exp_required')->unsigned();
            $table->integer('stat_points')->unsigned();
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
        Schema::dropIfExists('user_levels');
        Schema::dropIfExists('level_characters');
    }
}
