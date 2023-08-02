<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserShopsToLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items_log', function (Blueprint $table) {
            DB::statement("ALTER TABLE items_log MODIFY COLUMN sender_type ENUM('User', 'Character', 'Shop') DEFAULT 'User'");
            DB::statement("ALTER TABLE items_log MODIFY COLUMN recipient_type ENUM('User', 'Character', 'Shop') DEFAULT 'User'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log', function (Blueprint $table) {
            //
        });
    }
}
