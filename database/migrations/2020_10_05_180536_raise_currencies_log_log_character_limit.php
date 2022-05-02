<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RaiseCurrenciesLogLogCharacterLimit extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::table('currecies_log', function (Blueprint $table) {
            DB::statement('ALTER TABLE currencies_log MODIFY COLUMN log VARCHAR(255)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::table('currencies_log', function (Blueprint $table) {
            DB::statement('ALTER TABLE currencies_log MODIFY COLUMN log VARCHAR(191)');
        });
    }
}
