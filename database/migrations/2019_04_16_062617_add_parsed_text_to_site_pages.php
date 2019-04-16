<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParsedTextToSitePages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_pages', function (Blueprint $table) {
            //
            $table->text('parsed_text')->nullable()->default(null);

            // Drop this because pages are now not accessible unless explicitly
            // linked from another page.
            $table->dropColumn('is_listed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_pages', function (Blueprint $table) {
            //
            $table->boolean('is_listed')->default(1);
            $table->dropColumn('parsed_text');
        });
    }
}
