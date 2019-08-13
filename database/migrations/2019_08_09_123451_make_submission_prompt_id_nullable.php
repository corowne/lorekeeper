<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeSubmissionPromptIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make the prompt ID nullable, so we can use the same table for claims
        // The only difference between prompts and claims is that claims don't require a prompt selected.
        // We'll separate the prompts and claims queues though, so that they can be processed separately
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('prompt_id');
        });
        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('prompt_id')->unsigned()->nullable()->index();
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
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('prompt_id');
        });
        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('prompt_id')->unsigned()->index();
        });
    }
}
