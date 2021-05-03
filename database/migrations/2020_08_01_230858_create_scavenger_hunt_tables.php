<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScavengerHuntTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scavenger_hunts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name', 64);
            $table->string('display_name', 64);
            $table->string('summary', 256)->nullable()->default(null);
            // Space for a first clue.
            $table->string('clue', 256)->nullable()->default(null);

            // Space for a plaintext list of locations of targets.
            $table->text('locations')->nullable()->default(null);

            // Hunts are expected to be impermanent, so they need a start and end time.
            // They can't be interacted with outside of these times, but aren't really displayed either,
            // so this should suffice.
            $table->timestamp('start_at')->nullable()->default(null);
            $table->timestamp('end_at')->nullable()->default(null);
        });

        // Table for scavenger hunt targets.
        Schema::create('scavenger_targets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('hunt_id')->unsigned()->default(0);
            // Internal ID within the hunt, 1-10.
            $table->integer('target')->unsigned()->default(0);

            // Targets are expected to be an item of some quantity (usually 1),
            // but might as well support other quantities.
            $table->integer('item_id')->unsigned()->default(0);
            $table->integer('quantity')->unsigned();

            // Hunt targets will be assigned a randomized string,
            // since they need to be permalinked in a non-obvious way.
            $table->string('page_id', 64);

            // A short space for a description, clues, and the like.
            $table->text('description', 256)->nullable()->default(null);
        });

        // Table for scavenger hunt participants.
        // We need to be able to store if/when a user has found a target, so
        // we'll create a row each time a user participates in a different hunt.
        // This will also be used to populate the "logs" for individual hunts.
        Schema::create('scavenger_participants', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->unsigned()->default(0);
            $table->integer('hunt_id')->unsigned()->default(0);

            // Targets are indicated to be found by setting
            // a timestamp when the user finds them.
            // Targets are referred to by their ID within the hunt.
            $table->timestamp('target_1')->nullable()->default(null);
            $table->timestamp('target_2')->nullable()->default(null);
            $table->timestamp('target_3')->nullable()->default(null);
            $table->timestamp('target_4')->nullable()->default(null);
            $table->timestamp('target_5')->nullable()->default(null);
            $table->timestamp('target_6')->nullable()->default(null);
            $table->timestamp('target_7')->nullable()->default(null);
            $table->timestamp('target_8')->nullable()->default(null);
            $table->timestamp('target_9')->nullable()->default(null);
            $table->timestamp('target_10')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scavenger_hunts');
        Schema::dropIfExists('scavenger_targets');
        Schema::dropIfExists('scavenger_participants');
    }
}
