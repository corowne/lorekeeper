<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterLineageLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_lineage_links', function (Blueprint $table) {
            $table->id();

            $table->biginteger('lineage_id')->unsigned();
            $table->biginteger('parent_lineage_id')->unsigned();

            $table->foreign('lineage_id')->references('id')->on('character_lineages');
            $table->foreign('parent_lineage_id')->references('id')->on('character_lineages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_lineage_links');
    }
}
