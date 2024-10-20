<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->text('initial_prompt');

            $table->boolean('is_active')->default(1);
            $table->boolean('has_image')->default(0);
            $table->timestamp('start_at')->nullable()->default(null);
            $table->timestamp('end_at')->nullable()->default(null);
        });

        Schema::create('encounter_areas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 64);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_image')->default(0);
            $table->timestamp('start_at')->nullable()->default(null);
            $table->timestamp('end_at')->nullable()->default(null);
        });

        //encounter prompts
        Schema::create('encounter_prompts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('encounter_id')->unsigned();
            $table->string('name');
            $table->text('result');
        });

        //table for outputs to roll on for the areas
        Schema::create('area_encounters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('encounter_area_id')->unsigned();
            $table->integer('encounter_id')->unsigned();
            $table->integer('weight')->unsigned();  
            $table->foreign('encounter_area_id')->references('id')->on('encounter_areas');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('encounter_energy')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encounters');
        Schema::dropIfExists('encounter_areas');
        Schema::dropIfExists('encounter_prompts');
        Schema::dropIfExists('area_encounters');
    }
}
