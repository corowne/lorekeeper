<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReleasedToItems extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('items', function (Blueprint $table) {
            //
            $table->boolean('is_released')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('items', function (Blueprint $table) {
            //
            $table->dropColumn('is_released');
        });
    }
}
