<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditStatLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('stat_log', function (Blueprint $table)
        {
            $table->renameColumn('character_id', 'recipient_id');
            $table->enum('leveller_type', ['User', 'Character'])->nullable()->default('Character');
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
        Schema::table('stat_log', function (Blueprint $table)
        {
            $table->renameColumn('recipient_id', 'character_id');
            $table->dropColumn('leveller_type');
        });
    }
}
