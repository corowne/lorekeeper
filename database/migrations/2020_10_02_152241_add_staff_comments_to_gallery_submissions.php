<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaffCommentsToGallerySubmissions extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->text('staff_comments')->nullable();
            $table->text('parsed_staff_comments')->nullable();
            $table->integer('staff_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('gallery_submissions', function (Blueprint $table) {
            //
            $table->dropColumn('staff_comments');
            $table->dropColumn('parsed_staff_comments');
            $table->dropColumn('staff_id');
        });
    }
}
