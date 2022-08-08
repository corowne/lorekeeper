<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRankIcon extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('ranks', function (Blueprint $table) {
            $table->string('icon', 100)->after('color')->default('fas fa-user')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
}
