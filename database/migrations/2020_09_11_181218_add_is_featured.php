<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFeatured extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('comments', function (Blueprint $table) {
            $table->integer('is_featured')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
}
