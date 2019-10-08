<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMyoSlots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('characters', function (Blueprint $table) {            
            $table->boolean('is_myo_slot')->default(0);

            // MYO slots won't have these filled
            $table->integer('rarity_id')->unsigned()->nullable();
            $table->integer('character_category_id')->unsigned()->nullable()->change();
            $table->integer('number')->unsigned()->nullable()->change();
            $table->string('slug')->nullable()->change();
        });
        Schema::table('character_images', function (Blueprint $table) {       
            // MYO slots won't have these filled
            $table->integer('rarity_id')->unsigned()->nullable()->change();
            $table->integer('species_id')->unsigned()->nullable()->change();
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
        Schema::table('character_images', function (Blueprint $table) {       
            // MYO slots won't have these filled
            $table->integer('rarity_id')->unsigned()->change();
            $table->integer('species_id')->unsigned()->change();
        });
        Schema::table('characters', function (Blueprint $table) {            
            $table->dropColumn('is_myo_slot');

            // MYO slots won't have these filled
            $table->integer('rarity_id')->unsigned()->change();
            $table->integer('character_category_id')->unsigned()->change();
            $table->integer('number')->unsigned()->change();
            $table->string('slug')->change();
        });
    }
}
