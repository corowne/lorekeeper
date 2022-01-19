<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBugReportOption extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::table('reports', function (Blueprint $table) {
            $table->boolean('is_br')->default(0);
            $table->string('error_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('is_br');
            $table->dropColumn('error_type');
        });
    }
}
