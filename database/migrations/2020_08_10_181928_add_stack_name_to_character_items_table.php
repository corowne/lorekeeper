<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStackNameToCharacterItemsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('character_items', function (Blueprint $table) {
            //
            $table->text('stack_name')->nullable()->default(null);
        });

        Schema::table('item_categories', function (Blueprint $table) {
            //
            $table->boolean('can_name')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_items', function (Blueprint $table) {
            $table->dropColumn('stack_name');
        });
        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn('can_name');
        });
    }
}
