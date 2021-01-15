<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatMax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('stats', function (Blueprint $table) {
            $table->integer('max_level')->nullable()->unsigned()->default(null);
            $table->integer('user_level_req')->nullable()->unsigned()->default(null);
            $table->integer('character_level_req')->nullable()->unsigned()->default(null);
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
        Schema::table('stats', function (Blueprint $table) {
            $table->dropColumn('max_level');
            $table->dropColumn('user_level_req');
            $table->dropColumn('character_level_req');
        });
    }
}
