<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFullsizeHashToCharacterImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // To prevent people from scraping URLs
        Schema::table('character_images', function (Blueprint $table) {
            $table->string('fullsize_hash', 20);
        });

        Schema::table('design_updates', function (Blueprint $table) {
            $table->string('fullsize_hash', 20);
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
            $table->dropColumn('fullsize_hash');
        });

        Schema::table('design_updates', function (Blueprint $table) {
            $table->dropColumn('fullsize_hash');
        });
    }
}
