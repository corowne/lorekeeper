<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToGallerySubmissionCollaborators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
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
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gallery_submission_collaborators', function (Blueprint $table) {
            //
            $table->dropColumn('type');
        });
    }
}
