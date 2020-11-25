<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharacterLineageBlacklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ------------------------------------------
        // id | type     | type_id | complete_removal
        // ---|----------|---------|-----------------
        //  x | category | catID   | true (blacklist)
        //  x | species  | sID     | false (greylist)
        //  x | subtype  | stID    | true (blacklist)
        // ------------------------------------------
        // blacklist > greylist > default
        // ------------------------------------------
        Schema::create('character_lineage_blacklist', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->enum('type', ['category', 'species', 'subtype']);
            $table->integer('type_id')->unsigned();
            $table->boolean('complete_removal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_lineage_blacklist');
    }
}
