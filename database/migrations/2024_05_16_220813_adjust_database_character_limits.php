<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustDatabaseCharacterLimits extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('character_log', function (Blueprint $table) {
            $table->text('log')->change();
            $table->text('data')->nullable()->change();
        });

        Schema::table('character_items', function (Blueprint $table) {
            $table->text('data')->nullable()->change();
        });

        Schema::table('currencies_log', function (Blueprint $table) {
            $table->text('log')->nullable()->change();
        });

        Schema::table('items_log', function (Blueprint $table) {
            $table->text('log')->nullable()->change();
            $table->text('data')->nullable()->change();
        });

        Schema::table('user_character_log', function (Blueprint $table) {
            $table->text('log')->change();
            $table->text('data')->nullable()->change();
        });

        Schema::table('user_items', function (Blueprint $table) {
            $table->text('data')->nullable()->change();
        });

        Schema::table('user_update_log', function (Blueprint $table) {
            $table->text('data')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_log', function (Blueprint $table) {
            $table->string('log', 191)->change();
            $table->string('data', 1024)->nullable()->change();
        });

        Schema::table('character_items', function (Blueprint $table) {
            $table->string('data', 1024)->nullable()->change();
        });

        Schema::table('currencies_log', function (Blueprint $table) {
            $table->string('log', 512)->nullable()->change();
        });

        Schema::table('items_log', function (Blueprint $table) {
            $table->string('log', 1024)->nullable()->change();
            $table->string('data', 1024)->nullable()->change();
        });

        Schema::table('user_character_log', function (Blueprint $table) {
            $table->string('log', 191)->change();
            $table->string('data', 1024)->nullable()->change();
        });

        Schema::table('user_items', function (Blueprint $table) {
            $table->string('data', 1024)->nullable()->change();
        });

        Schema::table('user_update_log', function (Blueprint $table) {
            $table->string('data', 512)->change();
        });
    }
}
