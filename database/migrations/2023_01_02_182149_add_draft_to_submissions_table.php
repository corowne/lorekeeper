<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDraftToSubmissionsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        DB::statement('ALTER TABLE submissions modify status enum( "Draft", "Pending", "Approved", "Rejected" )');
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('submissions', function (Blueprint $table) {
            DB::statement('ALTER TABLE submissions modify status enum( "Pending", "Approved", "Rejected" )');
        });
    }
}
