<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransferReason extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // Add columns
        Schema::table('character_transfers', function (Blueprint $table) {
            $table->string('user_reason', 200)->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('character_transfers', function (Blueprint $table) {
            //
            $table->dropColumn('user_reason');
        });
    }
}
