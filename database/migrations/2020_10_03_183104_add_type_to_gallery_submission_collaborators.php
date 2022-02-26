<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToGallerySubmissionCollaborators extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gallery_submission_collaborators', function (Blueprint $table) {
            //
            $table->enum('type', ['Collab', 'Trade', 'Gift', 'Comm', 'Comm (Currency)'])->nullable()->default('Collab');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('gallery_submission_collaborators', function (Blueprint $table) {
            //
            $table->dropColumn('type');
        });
    }
}
