<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IWAddSecondarySpeciesToCharacters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_images', function (Blueprint $table) {
            $table->integer('secondary_species_id')->nullable(true)->unsigned();
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
            $table->dropColumn('secondary_species_id');
        });
    }

}
