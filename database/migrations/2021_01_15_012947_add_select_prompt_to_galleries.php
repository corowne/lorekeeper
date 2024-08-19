<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectPromptToGalleries extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('galleries', function (Blueprint $table) {
            //
            $table->boolean('prompt_selection')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('galleries', function (Blueprint $table) {
            //
            $table->dropColumn('prompt_selection');
        });
    }
}
