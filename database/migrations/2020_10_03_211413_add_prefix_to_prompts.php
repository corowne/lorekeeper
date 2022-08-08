<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefixToPrompts extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('prompts', function (Blueprint $table) {
            //
            $table->string('prefix', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('prompts', function (Blueprint $table) {
            //
            $table->dropColumn('prefix');
        });
    }
}
