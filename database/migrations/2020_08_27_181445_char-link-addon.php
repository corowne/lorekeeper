<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CharLinkAddon extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // Add columns
        Schema::table('character_profiles', function (Blueprint $table) {
            $table->string('link', 100)->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_profiles', function (Blueprint $table) {
            //
            $table->dropColumn('link');
        });
    }
}
