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
            $table->integer('rarity_id')->unsigned()->nullable()->change();
            $table->integer('character_category_id')->unsigned()->nullable()->change();
            $table->integer('number')->unsigned()->nullable()->change();
            $table->string('slug')->nullable()->change();
        });
        Schema::table('character_images', function (Blueprint $table) {       
            // MYO slots won't have these filled
            $table->integer('rarity_id')->unsigned()->nullable()->change();
            $table->integer('species_id')->unsigned()->nullable()->change();
        });
        /* 
        
            [design queue]
            - id
            - user id
            - submittable_type (myo or design update)
            - submittable_id (the myo slot id or character id)

            // these will be used in the generation of the new image row
            - species id
            - rarity id
            - url
            - extension
            - use_cropper
            - x0, x1, y0, y1

            // approval use
            - comments
            - staff_comments
            - staff id
            - status
            - timestamps

            [design queue features] // traits, will be converted to real traits
            - id
            - design queue id
            - feature id
            - data

            [design queue creators] // design credits, will be converted to real credits
            - design queue id
            - type
            - url
            - alias

            [design queue comments]
            - id
            - design queue id
            - staff id
            - staff comments
            - action (approve or reject)
            - created_at
        
        */
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
