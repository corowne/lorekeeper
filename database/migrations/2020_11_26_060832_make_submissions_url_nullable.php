<?php

use Illuminate\Database\Migrations\Migration;

class MakeSubmissionsUrlNullable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        //
        DB::statement('ALTER TABLE submissions CHANGE url url VARCHAR(200) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        //
        DB::statement('ALTER TABLE submissions CHANGE url url VARCHAR(200)');
    }
}
