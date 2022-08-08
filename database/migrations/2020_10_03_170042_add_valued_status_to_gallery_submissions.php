<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValuedStatusToGallerySubmissions extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->boolean('is_valued')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->dropColumn('is_valued');
        });
    }
}
