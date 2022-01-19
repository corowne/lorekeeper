<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCharacterTransferPermsToItemCategories extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('item_categories', function (Blueprint $table) {
            // Set whether an item category can be owned by characters.
            $table->boolean('is_character_owned')->default(0);

            // Limit for number of items of a category characters are allowed to hold without an admin manually adding them.
            $table->integer('character_limit')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn('is_character_owned');
            $table->dropColumn('character_limit');
        });
    }
}
