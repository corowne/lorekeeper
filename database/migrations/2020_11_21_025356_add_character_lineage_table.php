<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharacterLineageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create tables for storing character lineages.
        Schema::create('character_lineages', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            // Lineage and Character ID of the Child (Lineage-Holder)
            $table->increments('id');

            // Characters should only have ONE lineage assigned
            $table->integer('character_id')->unsigned()->unique();

            // Reduce errors by listing all the variants here...
            $ancestors = [
                'sire',
                'sire_sire',
                'sire_sire_sire',
                'sire_sire_dam',
                'sire_dam',
                'sire_dam_sire',
                'sire_dam_dam',
                'dam',
                'dam_sire',
                'dam_sire_sire',
                'dam_sire_dam',
                'dam_dam',
                'dam_dam_sire',
                'dam_dam_dam',
            ];

            // Character ID or an Identifying Name of the Child's
            // Parents / Grandparents / Great-Grandparents
            for ($i=0; $i < count($ancestors); $i++)
            {
                $table->integer($ancestors[$i].'_id')->unsigned()->nullable();
                $table->string($ancestors[$i].'_name')->nullable();
            }

            // Set references to the character ID table
            $table->foreign('character_id')->references('id')->on('characters');
            for ($i=0; $i < count($ancestors); $i++)
            {
                $table->foreign($ancestors[$i].'_id')->references('id')->on('characters');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_lineages');
    }
}
