<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserItemDonationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_item_donations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('stack_id')->index();
            $table->integer('item_id')->index();
            $table->integer('stock');
        });

        Schema::table('item_categories', function (Blueprint $table) {
            //
            $table->boolean('can_donate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_item_donations');

        Schema::table('item_categories', function (Blueprint $table) {
            //
            $table->dropColumn('can_donate');
        });
    }
}
