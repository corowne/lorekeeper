<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCharacterItemLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE character_items_log CHANGE `count` `quantity` INT(10) unsigned;');

        Schema::table('character_items_log', function (Blueprint $table) {
            $table->integer('stack_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE character_items_log CHANGE `quantity` `count` INT(10) unsigned;');

        Schema::table('character_items_log', function (Blueprint $table) {
            $table->integer('stack_id')->unsigned()->change();
        });
        
    }
}
