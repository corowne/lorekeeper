<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IWNonRollableSpecies extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specieses', function (Blueprint $table) {
            $table->boolean('can_be_rolled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specieses', function (Blueprint $table) {
            $table->boolean('can_be_rolled');
        });
    }

}
