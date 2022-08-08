<?php

use Illuminate\Database\Migrations\Migration;

class AddRejectedStatusToGallerySubmissions extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        DB::statement("ALTER TABLE gallery_submissions CHANGE COLUMN status status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        DB::statement("ALTER TABLE gallery_submissions CHANGE COLUMN status status ENUM('Pending', 'Accepted') DEFAULT 'Pending'");
    }
}
