<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentWarningToGallerySubmissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->string('content_warning', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->dropColumn('content_warning');
        });
    }
}
