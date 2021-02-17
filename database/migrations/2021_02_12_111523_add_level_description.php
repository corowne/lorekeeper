<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('level_users', function (Blueprint $table) {
            $table->text('description')->nullable()->default(null);
        });

        Schema::table('level_characters', function (Blueprint $table) {
            $table->text('description')->nullable()->default(null);
        });

        Schema::create('user_level_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('level_id');
            $table->string('limit_type');
            $table->unsignedInteger('limit_id');
            $table->integer('quantity')->unsigned();
        });

        Schema::create('character_level_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('level_id');
            $table->string('limit_type');
            $table->unsignedInteger('limit_id');
            $table->integer('quantity')->unsigned();
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
        Schema::table('level_users', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('level_characters', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::dropIfExists('user_level_requirements');
        Schema::dropIfExists('character_level_requirements');
    }
}
