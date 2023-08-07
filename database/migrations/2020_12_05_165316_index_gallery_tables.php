<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IndexGalleryTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('galleries', function (Blueprint $table) {
            //
            $table->index('parent_id');
        });

        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->index('gallery_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('galleries', function (Blueprint $table) {
            //
            $table->dropIndex(['parent_id']);
        });

        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->dropIndex(['gallery_id']);
            $table->dropIndex(['user_id']);
        });
    }
}
