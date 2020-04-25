<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOwnedCurrencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_currencies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('quantity')->unsigned()->default(0);

            //$table->unique(['user_id', 'currency_id']);
            $table->primary(['user_id', 'currency_id']);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
        Schema::create('character_currencies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('character_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('quantity')->unsigned()->default(0);

            //$table->unique(['character_id', 'currency_id']);
            $table->primary(['character_id', 'currency_id']);

            $table->foreign('character_id')->references('id')->on('characters');
            $table->foreign('currency_id')->references('id')->on('currencies');
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
    }
}
