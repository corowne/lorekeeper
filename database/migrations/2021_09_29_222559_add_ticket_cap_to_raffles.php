<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketCapToRaffles extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('raffles', function (Blueprint $table) {
            //
            $table->integer('ticket_cap')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            //
            $table->dropColumn('ticket_cap');
        });
    }
}
