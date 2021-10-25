<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRejectedStatusToGallerySubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE gallery_submissions CHANGE COLUMN status status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE gallery_submissions CHANGE COLUMN status status ENUM('Pending', 'Accepted') DEFAULT 'Pending'");
    }
}
