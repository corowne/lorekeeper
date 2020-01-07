<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCharacterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('characters', function (Blueprint $table) {
            $table->string('name')->nullable()->default(null);
            $table->boolean('is_gift_art_allowed')->default(0);
            $table->boolean('is_trading')->default(0);

            // MYO slots are considered separate now
            $table->dropColumn('is_myo_slot');
        });

        Schema::table('character_images', function (Blueprint $table) {
            $table->string('extension', 5);
            
            // Whether to use the image cropper to generate the thumbnail
            $table->boolean('use_cropper')->default(0);
            $table->integer('x0')->nullable()->default(null);
            $table->integer('x1')->nullable()->default(null);
            $table->integer('y0')->nullable()->default(null);
            $table->integer('y1')->nullable()->default(null);

            // To prevent people from scraping URLs
            $table->string('hash', 10);

            // Display order on profile
            $table->integer('sort')->default(0);

            // Marks if the image is valid.
            // This can be used in cases where you want to display
            // the image (an old version of the design) for logging purposes, 
            // but not allow the owner to use the design in the game
            $table->boolean('is_valid')->default(1);

            // This gets restrictive in cases where there are multiple
            // artists/designers (e.g. in collabs, or an updated design)
            // which is actually a fairly common occurrence,
            // so going to handle this in a separate table
            $table->dropColumn('artist_url');
            $table->dropColumn('artist_alias');
            $table->dropColumn('designer_url');
            $table->dropColumn('designer_alias');
        });

        Schema::create('character_image_creators', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('character_image_id')->unsigned();

            $table->enum('type', ['Artist', 'Designer']);
            $table->string('url')->nullable()->default(null);
            $table->string('alias')->nullable()->default(null);

            $table->foreign('character_image_id')->references('id')->on('character_images');
        });

        Schema::create('character_profiles', function (Blueprint $table) { 
            $table->engine = 'InnoDB';           
            $table->integer('character_id')->unsigned();

            $table->text('text')->nullable()->default(null);
            $table->text('parsed_text')->nullable()->default(null);

            $table->foreign('character_id')->references('id')->on('characters');
        });

        Schema::create('user_profiles', function (Blueprint $table) {     
            $table->engine = 'InnoDB';       
            $table->integer('user_id')->unsigned();

            $table->text('text')->nullable()->default(null);
            $table->text('parsed_text')->nullable()->default(null);

            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('character_profiles');
        Schema::dropIfExists('character_image_creators');

        Schema::table('character_images', function (Blueprint $table) {
            $table->dropColumn('extension');
            $table->dropColumn('use_cropper');
            $table->dropColumn('x0');
            $table->dropColumn('x1');
            $table->dropColumn('y0');
            $table->dropColumn('y1');
            $table->dropColumn('hash');
            $table->dropColumn('sort');
            $table->dropColumn('is_valid');
            
            $table->string('designer_alias');
            $table->string('designer_url');
            $table->string('artist_alias');
            $table->string('artist_url');
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('is_gift_art_allowed');
            $table->dropColumn('is_trading');

            $table->boolean('is_myo_slot')->default(0);
        });
    }
}
