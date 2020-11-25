<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCharacterLineageBlacklist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `character_lineage_blacklist` CHANGE `type` `type` ENUM('category','species','subtype','rarity')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `character_lineage_blacklist` CHANGE `type` `type` ENUM('category','species','subtype')");
    }
}
