<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVotesToDesignUpdates extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->string('vote_data', 512)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->dropColumn('vote_data');
        });
    }
}
