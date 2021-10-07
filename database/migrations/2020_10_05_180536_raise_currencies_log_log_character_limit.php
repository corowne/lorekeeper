<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RaiseCurrenciesLogLogCharacterLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
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
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('currencies_log', function (Blueprint $table) {
            DB::statement('ALTER TABLE currencies_log MODIFY COLUMN log VARCHAR(191)');
        });
    }
}
