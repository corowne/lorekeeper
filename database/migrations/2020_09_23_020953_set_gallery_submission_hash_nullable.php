<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetGallerySubmissionHashNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('gallery_submissions', function (Blueprint $table) {
            $table->dropColumn('hash');
            $table->dropColumn('extension');
        });

        Schema::table('gallery_submissions', function (Blueprint $table) {
            $table->string('hash', 10)->nullable();
            $table->string('extension', 5)->nullable();
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
        Schema::table('gallery_submissions', function (Blueprint $table) {
            $table->dropColumn('hash');
            $table->dropColumn('extension');
        });

        Schema::table('gallery_submissions', function (Blueprint $table) {
            $table->string('hash', 10);
            $table->string('extension', 5);
        });
    }
}
