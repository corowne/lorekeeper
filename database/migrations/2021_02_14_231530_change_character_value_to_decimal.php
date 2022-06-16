<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCharacterValueToDecimal extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->decimal('sale_value', 13, 2)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->integer('sale_value')->nullable(false)->default(0)->change();
        });
    }
}
