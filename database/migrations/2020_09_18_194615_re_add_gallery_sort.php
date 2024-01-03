<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReAddGallerySort extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('galleries', function (Blueprint $table) {
            //
            $table->integer('sort')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('galleries', function (Blueprint $table) {
            //
            $table->dropColumn('sort');
        });
    }
}
