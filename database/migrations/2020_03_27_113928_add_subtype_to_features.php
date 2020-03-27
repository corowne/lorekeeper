<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubtypeToFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This column was added to the wrong table
        Schema::table('character_features', function(Blueprint $table) {
            $table->dropColumn('subtype_id');
        });
        Schema::table('features', function (Blueprint $table) {
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
        Schema::table('features', function(Blueprint $table) {
            $table->dropColumn('subtype_id');
        });
        Schema::table('character_features', function (Blueprint $table) {
            $table->integer('subtype_id')->unsigned()->nullable()->default(null);
        });
    }
}
