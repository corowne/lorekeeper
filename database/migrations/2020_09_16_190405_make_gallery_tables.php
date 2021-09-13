<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeGalleryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Gallery structure table
        Schema::create('galleries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            // Parent gallery ID
            $table->integer('parent_id')->unsigned()->nullable();

            $table->string('name', 100);
            $table->integer('sort')->unsigned();
            $table->text('description')->nullable()->default(null);

            // Whether or not submissions to this gallery
            // are eligible for group currency rewards.
            $table->boolean('currency_enabled')->default(0);

            // Votes required for a submission to this gallery to be accepted,
            // and whether or not the gallery is accepting submissions.
            // Does not override global gallery submission open/close setting.
            // Admins disregard this but not the global setting.
            $table->integer('votes_required')->unsigned()->default(0);
            $table->boolean('submissions_open')->default(1);
        });

        // Gallery submission table
        Schema::create('gallery_submissions', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('gallery_id')->unsigned();

            // Submitting user and data for any collaborators
            $table->integer('user_id')->unsigned();
            $table->string('collaborator_data', 1024)->nullable()->default(null);

            // Submission itself-- either info on the image
            // or submitted literature (or both, for literature
            // with accompanying image)
            $table->string('hash', 10);
            $table->string('extension', 5);
            $table->text('text')->nullable();
            $table->text('parsed_text')->nullable();

            // Description
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();

            // Extended data
            // Prompt ID if associated with a prompt
            $table->integer('prompt_id')->unsigned()->nullable()->default(null);
            // Data from currency form, submission ID?
            $table->string('data', 1024)->nullable()->default(null);
            // Attached characters, since only the ID needs to be stored
            $table->string('character_data', 1024)->nullable()->default(null);

            // Visibility and approval data
            $table->boolean('is_visible')->default(1);
            $table->enum('status', ['Pending', 'Accepted'])->nullable()->default('Pending');
            $table->string('vote_data', 512)->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('gallery_submissions');
    }
}
