<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatTransferLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('stat_transfer_log', function(Blueprint $table) {
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
            $table->enum('recipient_type', ['User', 'Character'])->nullable()->default('Character');
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
        Schema::dropIfExists('stat_transfer_log');
    }
}
