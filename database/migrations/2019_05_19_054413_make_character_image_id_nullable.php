<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCharacterImageIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Realised I couldn't attach an image to a character without making either the 
        // character's image ID nullable, or making the image's character ID nullable...
        // Also, since characters can now be credited to users who don't have an
        // account, make this column nullable.
        Schema::table('characters', function (Blueprint $table) {
            //$table->dropForeign('characters_user_id_foreign');

            $table->dropColumn('character_image_id');
            $table->dropColumn('user_id');
        });
        Schema::table('characters', function (Blueprint $table) {
            $table->integer('character_image_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
        });

        // This also isn't necessary
        Schema::table('character_images', function (Blueprint $table) {
            $table->dropForeign('character_images_user_id_foreign');
            $table->dropColumn('user_id');
        });

        // Modifying these to log user alias,
        // since characters created before the site was created will
        // need to be credited to users who don't have an account
        // and new sales may need to be credited to users before they 
        // create an account.
        // However, retaining the recipient ID since characters
        // may also need to be logged to an account without an alias.
        Schema::table('character_log', function (Blueprint $table) {
            $table->dropColumn('recipient_id');
            $table->string('recipient_alias')->nullable()->default(null);
        });
        Schema::table('character_log', function (Blueprint $table) {
            $table->integer('recipient_id')->unsigned()->nullable()->default(null);
        });
        Schema::table('user_character_log', function (Blueprint $table) {
            $table->dropColumn('recipient_id');
            $table->string('recipient_alias')->nullable()->default(null);
        });
        Schema::table('user_character_log', function (Blueprint $table) {
            $table->integer('recipient_id')->unsigned()->nullable()->default(null);
        });

        // Data for traits also needs to be nullable.
        Schema::table('character_features', function (Blueprint $table) {
            $table->dropColumn('data');
        });
        Schema::table('character_features', function (Blueprint $table) {
            $table->string('data')->nullable()->default(null);
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
            $table->dropColumn('data');
        });
        Schema::table('character_features', function (Blueprint $table) {
            $table->string('data');
        });
        Schema::table('user_character_log', function (Blueprint $table) {
            $table->dropColumn('recipient_id');
        });
        Schema::table('user_character_log', function (Blueprint $table) {
            $table->dropColumn('recipient_alias');
            $table->integer('recipient_id')->unsigned();
        });
        Schema::table('character_log', function (Blueprint $table) {
            $table->dropColumn('recipient_id');
        });
        Schema::table('character_log', function (Blueprint $table) {
            $table->dropColumn('recipient_alias');
            $table->integer('recipient_id')->unsigned();
        });
        Schema::table('character_images', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('character_image_id');
        });
        Schema::table('characters', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('character_image_id')->unsigned();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
