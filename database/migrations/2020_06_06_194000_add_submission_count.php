<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubmissionCount extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_items', function (Blueprint $table) {
            $table->unsignedInteger('submission_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_items', function (Blueprint $table) {
            //
            $table->dropColumn('submission_count');
        });
    }
}
