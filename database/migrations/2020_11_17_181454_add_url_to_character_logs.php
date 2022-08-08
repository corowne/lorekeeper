<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToCharacterLogs extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('character_log', function (Blueprint $table) {
            //
            $table->string('sender_url')->nullable()->default(null);
            $table->string('recipient_url')->nullable()->default(null);
        });

        Schema::table('user_character_log', function (Blueprint $table) {
            //
            $table->string('sender_url')->nullable()->default(null);
            $table->string('recipient_url')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_log', function (Blueprint $table) {
            //
            $table->dropColumn('sender_url');
            $table->dropColumn('recipient_url');
        });

        Schema::table('user_character_log', function (Blueprint $table) {
            //
            $table->dropColumn('sender_url');
            $table->dropColumn('recipient_url');
        });
    }
}
