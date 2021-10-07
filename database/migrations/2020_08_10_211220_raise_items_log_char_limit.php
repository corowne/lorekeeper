<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RaiseItemsLogCharLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('items_log', function (Blueprint $table) {
            DB::statement('ALTER TABLE items_log MODIFY COLUMN log VARCHAR(1024)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('items_log', function (Blueprint $table) {
            DB::statement('ALTER TABLE items_log MODIFY COLUMN log VARCHAR(191)');
        });
    }
}
