<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixInventoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Turns out I made a user_currencies table on top of the banks one,
        // so gonna remove the banks ones and rename the logs,
        // and rename the inventory tables to match the pattern
        Schema::dropIfExists('banks');
        Schema::rename('banks_log', 'currencies_log');
        Schema::rename('inventory', 'user_items');
        Schema::rename('inventory_log', 'user_items_log');
        
        Schema::table('currencies_log', function (Blueprint $table) {
            $table->dropColumn('data');
            $table->dropColumn('type');
        });
        Schema::table('currencies_log', function (Blueprint $table) {
            // Standardise with the item logs
            $table->string('log')->nullable();
            $table->string('log_type'); 
            $table->string('data', 1024)->nullable();
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
        Schema::table('currencies_log', function (Blueprint $table) {

            $table->dropColumn('log');
            $table->dropColumn('log_type');
            $table->dropColumn('data');
        });
        Schema::table('currencies_log', function (Blueprint $table) {

            $table->string('type', 32);
            $table->string('data', 512);
        });


        Schema::rename('user_items_log', 'inventory_log');
        Schema::rename('user_items', 'inventory');
        Schema::rename('currencies_log', 'banks_log');

        Schema::create('banks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('character_id')->unsigned()->nullable();
            $table->integer('currency_id')->unsigned();

            $table->integer('quantity')->default(0);
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('character_id')->references('id')->on('characters');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }
}
