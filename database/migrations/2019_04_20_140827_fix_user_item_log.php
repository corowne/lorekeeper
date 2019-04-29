<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixUserItemLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add sign to quantities, also rename count in the items log
        Schema::table('user_items_log', function(Blueprint $table) {
            $table->dropColumn('count');
            $table->integer('quantity')->unsigned()->default(1);
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
        Schema::table('user_items_log', function(Blueprint $table) {
            $table->dropColumn('quantity');
            $table->integer('count')->unsigned()->default(1);
        });
    }
}
