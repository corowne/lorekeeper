<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpeciesIdToImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            //$table->dropForeign('characters_rarity_id_foreign');

            // Moving this onto images as it can change
            $table->dropColumn('rarity_id');

            $table->string('owner_alias')->nullable()->default('null');
        });
        Schema::table('character_images', function (Blueprint $table) {
            

            $table->integer('species_id')->unsigned();
            $table->integer('rarity_id')->unsigned();

            $table->foreign('species_id')->references('id')->on('specieses');
            $table->foreign('rarity_id')->references('id')->on('rarities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_images', function (Blueprint $table) {
            //
            $table->dropForeign('character_images_species_id_foreign');
            $table->dropForeign('character_images_rarity_id_foreign');
            $table->dropColumn('species_id');
            $table->dropColumn('rarity_id');
            
            
        });
        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign('characters_rarity_id_foreign');

            $table->dropColumn('owner_alias');

            $table->integer('rarity_id')->unsigned();
            $table->foreign('rarity_id')->references('id')->on('rarities');
        });
    }
}
