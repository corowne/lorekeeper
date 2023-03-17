<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraDataToUserAliases extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_aliases', function (Blueprint $table) {
            $table->string('user_snowflake')->nullable()->default(null);
        });

        Schema::table('users', function (Blueprint $table) {
            // Making email and password nullable
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_aliases', function (Blueprint $table) {
            $table->dropColumn('user_snowflake');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
}
