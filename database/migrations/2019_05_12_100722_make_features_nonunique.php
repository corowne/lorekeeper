<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFeaturesNonunique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This probably sounds like a pretty weird change,
        // but making it so that you can attach multiple of the
        // same trait to a character.
        // This is because you might want to attach e.g. multiple
        // familiar traits to a character, one for each familiar... 
        // each one can just have a different description.
        Schema::table('character_features', function (Blueprint $table) {
            $table->dropIndex('character_features_character_image_id_feature_id_unique');

            $table->increments('id');
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
        Schema::table('character_features', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->unique(['character_image_id', 'feature_id']);
        });
    }
}
