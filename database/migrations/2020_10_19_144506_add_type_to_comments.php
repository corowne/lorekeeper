<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToComments extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('comments', function (Blueprint $table) {
            //
            $table->string('type')->default('User-User');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('comments', function (Blueprint $table) {
            //
            $table->dropColumn('type');
        });
    }
}
