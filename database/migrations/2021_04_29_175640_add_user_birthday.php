<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserBirthday extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('birthday')->nullable()->default(null);
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->tinyInteger('birthday_setting')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('birthday');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropcolumn('birthday_setting');
        });
    }
}
