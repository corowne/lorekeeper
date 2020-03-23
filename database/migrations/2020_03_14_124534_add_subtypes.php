<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubtypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // These are basically, subspecies - I've decided to name them "subtypes"
        // as "subspecies" implies a biological taxonomic relation,
        // when "subspecies" in closed species tend to be less strictly scientific
        // and could be magical, could be dynamically changed... 
        // so something looser like "subtype" would probably be better as a blanket term
        // for these.
        Schema::create('subtypes', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('species_id')->unsigned();

            $table->string('name');
            $table->boolean('has_image')->default(0);
            $table->integer('sort')->unsigned()->default(0);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->foreign('species_id')->references('id')->on('specieses');
        });
        Schema::table('character_images', function(Blueprint $table) {
            $table->integer('subtype_id')->unsigned()->nullable()->default(null);
        });
        Schema::table('design_updates', function(Blueprint $table) {
            $table->integer('subtype_id')->unsigned()->nullable()->default(null);
        });
        Schema::table('character_features', function(Blueprint $table) {
            $table->integer('subtype_id')->unsigned()->nullable()->default(null);
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
        Schema::table('character_images', function(Blueprint $table) {
            $table->dropColumn('subtype_id');
        });
        Schema::table('design_updates', function(Blueprint $table) {
            $table->dropColumn('subtype_id');
        });
        Schema::table('character_features', function(Blueprint $table) {
            $table->dropColumn('subtype_id');
        });
        Schema::dropIfExists('subtypes');
    }
}
