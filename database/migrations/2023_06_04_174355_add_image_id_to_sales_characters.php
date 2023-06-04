<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('sales_characters', function (Blueprint $table) {
            $table->integer('image_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('sales_characters', function (Blueprint $table) {
            $table->dropColumn('image_id');
        });
    }
};
