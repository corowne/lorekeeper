<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValuedStatusToGallerySubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->boolean('is_valued')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->dropColumn('is_valued');
        });
    }
}
