<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecipeCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Adding recipe categories as a table in the database as well as adding a recipe cateogry id to recipes for the recipes to reference.
        Schema::create('recipe_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('name');
            $table->integer('sort')->unsigned()->default(0);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->boolean('has_image')->default(0);
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->integer('recipe_category_id')->unsigned()->nullable()->default(null);
            $table->foreign('recipe_category_id')->references('id')->on('recipe_categories');
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
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_categories');
    }
}
