<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('exp_log', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('quantity')->default(1);

            $table->integer('sender_id')->unsigned()->nullable();
            $table->integer('recipient_id')->unsigned()->nullable();
            
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Indicates what type of transaction the item was used in
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            $table->timestamps();

            //Add sender and recipient type. Set default user to account for preexisting rows
            $table->enum('sender_type', ['User', 'Character'])->nullable()->default('User');
            $table->enum('recipient_type', ['User', 'Character'])->nullable()->default('User');
        });

        Schema::create('level_log', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->enum('leveller_type', ['User', 'Character'])->nullable()->default('User');
            $table->integer('previous_level')->unsigned();
            $table->integer('new_level')->unsigned();

            $table->timestamps();

        });

        Schema::create('stat_log', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('character_id')->unsigned()->nullable();
            $table->integer('stat_id')->unsigned();
            $table->integer('previous_level')->unsigned();
            $table->integer('new_level')->unsigned();

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
        //
        Schema::dropIfExists('exp_log');
        Schema::dropIfExists('level_log');
        Schema::dropIfExists('stat_log');
    }
}
