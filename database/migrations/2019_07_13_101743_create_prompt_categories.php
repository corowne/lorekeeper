<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromptCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Prompt categories are for neatly categorising prompts, e.g. regular prompts and event-specific prompts
        Schema::create('prompt_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->integer('sort')->unsigned()->default(0);
            
            $table->boolean('has_image')->default(0);
        });
        Schema::table('prompts', function (Blueprint $table) {
            $table->integer('prompt_category_id')->unsigned()->default(0);
            $table->foreign('prompt_category_id')->references('id')->on('prompt_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn('prompt_category_id');
        });
        Schema::dropIfExists('prompt_categories');
    }
}
