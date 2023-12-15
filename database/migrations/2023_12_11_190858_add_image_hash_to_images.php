<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageHashToImages extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('subtypes', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('specieses', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('shops', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('prompt_categories', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('prompts', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('item_categories', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('items', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('feature_categories', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('features', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('character_categories', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
        Schema::table('rarities', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('subtypes', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('specieses', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('prompt_categories', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('feature_categories', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('character_categories', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
        Schema::table('rarities', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
    }
}
