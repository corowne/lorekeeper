<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixDataDescriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // OK...I decided I wanted to make these all consistent, and also allow HTML,
        // which means I need a field for the parsed descriptions.
        // Mildly concerned this is going to bulk up the tables, but I don't think
        // performance would be a concern on masterlist sites in general...?
        
        // One of my earlier concerns was fitting descriptions into tooltips, but thinking about it, 
        // it's better to just link them to the data entries directly...
        // I'll eventually add something that'll parse spoiler boxes in text so that 
        // lengthy information can be collapsed by the description writer.

        Schema::table('ranks', function(Blueprint $table) {
            $table->dropColumn('description');
        });
            
        Schema::table('ranks', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('rarities', function(Blueprint $table) {
            $table->dropColumn('description');
        });
        
        Schema::table('rarities', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('item_categories', function(Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('item_categories', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('items', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('specifications');
        });

        Schema::table('items', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('specieses', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('specifications');
        });

        Schema::table('specieses', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('feature_categories', function(Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('feature_categories', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('features', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('specifications');
        });

        Schema::table('features', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('character_categories', function(Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('character_categories', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('character_images', function(Blueprint $table) {
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('characters', function(Blueprint $table) {
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('character_submissions', function(Blueprint $table) {
            $table->text('parsed_description')->nullable()->default(null);
        });
        
        Schema::table('currencies', function(Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('currencies', function(Blueprint $table) {
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
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
        Schema::table('currencies', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });

        Schema::table('currencies', function(Blueprint $table) {
            $table->string('description', 512)->nullable()->default(null);
        });

        Schema::table('character_submissions', function(Blueprint $table) {
            $table->dropColumn('parsed_description');
        });
        
        Schema::table('characters', function(Blueprint $table) {
            $table->dropColumn('parsed_description');
        });
        
        Schema::table('character_images', function(Blueprint $table) {
            $table->dropColumn('parsed_description');
        });
        
        Schema::table('character_categories', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });

        Schema::table('character_categories', function(Blueprint $table) {
            $table->string('description', 512);
        });
        
        Schema::table('features', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });

        Schema::table('features', function(Blueprint $table) {
            $table->string('description', 512);
            $table->text('specifications')->nullable()->default(null);
        });
        
        Schema::table('feature_categories', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });
            
        Schema::table('feature_categories', function(Blueprint $table) {
            $table->string('description', 512);
        });
        
        Schema::table('specieses', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });
            
        Schema::table('specieses', function(Blueprint $table) {
            $table->string('description', 512);
            $table->text('specifications')->nullable()->default(null);
        });
        
        Schema::table('items', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });

        Schema::table('items', function(Blueprint $table) {
            $table->string('description', 512);
            $table->text('specifications')->nullable()->default(null);
        });
        
        Schema::table('item_categories', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });

        Schema::table('item_categories', function(Blueprint $table) {
            $table->string('description', 512);
        });
        
        Schema::table('rarities', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });

        Schema::table('rarities', function(Blueprint $table) {
            $table->string('description', 512);
        });

        Schema::table('ranks', function(Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('parsed_description');
        });
        
        Schema::table('ranks', function(Blueprint $table) {
            $table->string('description', 512)->nullable();
        });
    }
}
