<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrEditCharacterLineages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // checks if the original character lineages been installed.
        if (Schema::hasTable('character_lineages'))
        {
            // it has, so add the new field and edit the old.
            Schema::table('character_lineages', function (Blueprint $table) {
                DB::statement('ALTER TABLE `character_lineages` MODIFY `character_id` INTEGER UNSIGNED UNIQUE NULL;');
                
                if (!Schema::hasColumn('character_lineages', 'character_name'))
                    $table->string('character_name')->after('character_id')->nullable();
            });
        }
        else // old lineages has not been installed, create the table.
        {
            Schema::create('character_lineages', function (Blueprint $table) {
                $table->id();

                $table->integer('character_id')->unsigned()->unique()->nullable();
                $table->foreign('character_id')->references('id')->on('characters');

                $table->string('character_name')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // checks if the original character lineages been installed.
        if (Schema::hasColumn('character_lineages', 'sire_id'))
        {
            // it has, so reverse the new changes.
            Schema::table('character_lineages', function (Blueprint $table) {
                DB::statement('ALTER TABLE `character_lineages` MODIFY `character_id` INTEGER UNSIGNED UNIQUE NOT NULL;');
                $table->dropColumn('character_name');
            });
        }
        else // old lineages has not been installed.
        {
            Schema::dropIfExists('character_lineages');
        }
    }
}
