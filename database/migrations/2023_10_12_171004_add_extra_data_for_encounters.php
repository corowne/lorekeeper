<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraDataForEncounters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->text('extras')->nullable()->default(null);
        });
        Schema::table('encounter_areas', function (Blueprint $table) {
            $table->boolean('has_thumbnail')->default(0);
        });
        Schema::table('encounter_prompts', function (Blueprint $table) {
            $table->text('output')->nullable()->default(null);
            $table->text('extras')->nullable()->default(null);
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
    }
}
