<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGalleryFavoritesTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::create('gallery_favorites', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('gallery_submission_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::dropIfExists('gallery_favorites');
    }
}
