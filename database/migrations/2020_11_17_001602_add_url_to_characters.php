<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlToCharacters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('characters', function (Blueprint $table) {
            // Create a column to house owner URL
            $table->string('owner_url')->nullable()->default(null)->index();
        });

        Schema::table('character_image_creators', function (Blueprint $table) {
            // Index the url column here as well since it's now doing heavier duty
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            //
            $table->dropColumn('owner_url');
        });

        Schema::table('character_image_creators', function (Blueprint $table) {
            //
            $table->dropIndex(['url']);
        });
    }
}
