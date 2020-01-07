<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This will contain art prompts that users can submit art under.
        // Prompts can have a start and end time, so event-only prompts can be
        // made and future prompts can be queued.
        Schema::create('prompts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 64);

            // The summary will be displayed on the world page, 
            // with a link to a page that contains the full text of the prompt.
            $table->string('summary', 256)->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            // The active flag is overridden by the start_at and end_at timestamps,
            // i.e. if either or both of those timestamps are set,
            // it will have no effect.
            $table->boolean('is_active')->default(1);
            $table->timestamp('start_at')->nullable()->default(null);
            $table->timestamp('end_at')->nullable()->default(null);
            // When submitting a prompt, the selectable list will only contain prompts between
            // the start/end times and active prompts.

            // This hides the prompt from the world prompt list before 
            // the prompt start_at time has been reached.
            $table->boolean('hide_before_start')->default(0);

            // This hides the prompt from the world prompt list after 
            // the prompt end_at time has been reached.
            $table->boolean('hide_after_end')->default(0);
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
        Schema::dropIfExists('prompts');
    }
}
