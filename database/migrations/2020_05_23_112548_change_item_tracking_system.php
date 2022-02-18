<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeItemTrackingSystem extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('ALTER TABLE user_items CHANGE `holding_count` `trade_count` INT(10) unsigned;');

        Schema::table('user_items', function (Blueprint $table) {
            $table->unsignedInteger('update_count')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE user_items CHANGE `trade_count` `holding_count` INT(10) unsigned;');

        Schema::table('user_items', function (Blueprint $table) {
            $table->dropColumn('update_count');
        });
    }
}
