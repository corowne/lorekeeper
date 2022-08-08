<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RaiseItemsLogCharLimit extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('items_log', function (Blueprint $table) {
            DB::statement('ALTER TABLE items_log MODIFY COLUMN log VARCHAR(1024)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('items_log', function (Blueprint $table) {
            DB::statement('ALTER TABLE items_log MODIFY COLUMN log VARCHAR(191)');
        });
    }
}
