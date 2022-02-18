<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToggleForCommentsOnSitePages extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::table('site_pages', function (Blueprint $table) {
            $table->integer('can_comment')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::table('site_pages', function (Blueprint $table) {
            $table->dropColumn('can_comment');
        });
    }
}
