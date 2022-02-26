<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemCategoryInfoToLoots extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('loots', function (Blueprint $table) {
            //
            $table->string('data')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('loots', function (Blueprint $table) {
            //
            $table->dropColumn('data');
        });
    }
}
