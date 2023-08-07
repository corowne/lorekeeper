<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFullsizeHashToCharacterImages extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
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
     */
    public function down() {
        Schema::table('character_images', function (Blueprint $table) {
            $table->dropColumn('fullsize_hash');
        });

        Schema::table('design_updates', function (Blueprint $table) {
            $table->dropColumn('fullsize_hash');
        });
    }
}
