<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaffCommentsToGallerySubmissions extends Migration
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
            $table->text('staff_comments')->nullable();
            $table->text('parsed_staff_comments')->nullable();
            $table->integer('staff_id')->unsigned()->nullable();
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
            $table->dropColumn('staff_comments');
            $table->dropColumn('parsed_staff_comments');
            $table->dropColumn('staff_id');
        });
    }
}
