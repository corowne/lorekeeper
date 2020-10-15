<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToDesignUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->enum('update_type', ['MYO', 'Character'])->nullable()->default('Character');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->dropColumn('update_type');
        });
    }
}
