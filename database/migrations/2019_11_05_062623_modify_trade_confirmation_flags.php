<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTradeConfirmationFlags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn('is_confirmed');
            $table->boolean('is_sender_trade_confirmed')->default(0);
            $table->boolean('is_recipient_trade_confirmed')->default(0);
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
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn('is_sender_trade_confirmed');
            $table->dropColumn('is_recipient_trade_confirmed');
            $table->boolean('is_confirmed')->default(0);
        });
    }
}
