<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ItemEntryExtension extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns for rarity, reference link, artist alias/URL, availability, use(s)
        Schema::table('items', function (Blueprint $table) {
            $table->string('data', 1024)->nullable(); // includes rarity and availability information.
            $table->string('reference_url', 200)->nullable();
            $table->string('artist_alias')->nullable();
            $table->string('artist_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            //
            $table->dropColumn('data');
            $table->dropColumn('reference_url');
            $table->dropColumn('artist_alias');
            $table->dropColumn('artist_url');
        });
    }
}
