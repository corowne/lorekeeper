<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecureTrades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('trades', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->enum('status', ['Open', 'Pending', 'Completed', 'Rejected', 'Canceled'])->default('Open');

            $table->integer('sender_id')->unsigned();
            $table->integer('recipient_id')->unsigned();
            $table->text('comments')->nullable()->default(null);

            $table->boolean('is_sender_confirmed')->default(0);
            $table->boolean('is_recipient_confirmed')->default(0);
            $table->boolean('is_confirmed')->default(0);
            $table->boolean('is_approved')->default(0);
            
            // Reason for the transfer being rejected by a mod
            $table->text('reason')->nullable()->default(null);

            // Information including added cooldowns
            $table->string('data', 1024)->nullable()->default(null);

            $table->timestamps();
        });
        Schema::table('characters', function (Blueprint $table) {
            // Fill this with a trade ID so we can tell if the character is busy
            $table->integer('trade_id')->unsigned()->nullable()->default(null);
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
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('trade_id');
        });
        Schema::dropIfExists('trades');
    }
}
