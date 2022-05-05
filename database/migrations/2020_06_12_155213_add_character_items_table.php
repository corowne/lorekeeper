<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Create tables for storing character-owned items and the associated logs.
        Schema::create('character_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('character_id')->unsigned();

            $table->integer('count')->unsigned()->default(1);

            $table->string('data', 1024)->nullable(); // includes information like staff notes, etc.

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('character_id')->references('id')->on('characters');
        });

        Schema::create('character_items_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('count')->unsigned()->default(1);
            $table->integer('stack_id')->unsigned();

            $table->integer('sender_id')->unsigned()->nullable();
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Indicates what type of transaction the item was used in
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::dropIfExists('character_items');
        Schema::dropIfExists('character_items_log');
    }
}
