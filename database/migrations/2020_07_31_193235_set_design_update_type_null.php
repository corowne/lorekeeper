<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDesignUpdateTypeNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Change default to null going forward
        DB::statement("ALTER TABLE design_updates CHANGE COLUMN update_type update_type ENUM('MYO', 'Character') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement("ALTER TABLE design_updates CHANGE COLUMN update_type update_type ENUM('MYO', 'Character') DEFAULT 'Character'");
    }
}
