<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransferReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns
        Schema::table('character_transfers', function (Blueprint $table) {
            $table->string('user_reason', 200)->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_transfers', function (Blueprint $table) {
            //
            $table->dropColumn('user_reason');
        });
    }
}
