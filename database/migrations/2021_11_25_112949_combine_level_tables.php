<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CombineLevelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // rename level_users to levels
        Schema::rename('level_users', 'levels');
        Schema::table('levels', function (Blueprint $table) {
            $table->enum('level_type', ['User', 'Character'])->default('User');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // cannot be undone
    }
}
