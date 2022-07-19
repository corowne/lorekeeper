<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArtistIdToItems extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('items', function (Blueprint $table) {
            //
            $table->integer('artist_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('items', function (Blueprint $table) {
            //
            $table->dropColumn('artist_id');
        });
    }
}
