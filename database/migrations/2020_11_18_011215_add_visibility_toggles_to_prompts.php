<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityTogglesToPrompts extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('prompts', function (Blueprint $table) {
            //
            $table->integer('hide_submissions')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('prompts', function (Blueprint $table) {
            //
            $table->dropColumn('hide_submissions');
        });
    }
}
