<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChangedDataToCharacterLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_log', function (Blueprint $table) {
            // This will store the specifics of the changes made
            $table->text('change_log')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_log', function (Blueprint $table) {
            //
            $table->dropColumn('change_log');
        });
    }
}
