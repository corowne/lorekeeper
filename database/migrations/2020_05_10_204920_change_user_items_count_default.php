<?php

use Illuminate\Database\Migrations\Migration;

class ChangeUserItemsCountDefault extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        DB::statement('ALTER TABLE user_items ALTER count SET DEFAULT 0;');
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        DB::statement('ALTER TABLE user_items ALTER count SET DEFAULT 1;');
    }
}
