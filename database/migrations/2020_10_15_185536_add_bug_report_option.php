<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBugReportOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
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
     *
     * @return void
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
