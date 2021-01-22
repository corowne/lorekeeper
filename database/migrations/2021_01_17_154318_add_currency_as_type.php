<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyAsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->dropColumn('ingredient_type');
        });
        
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->enum('ingredient_type', ['Item', 'MultiItem', 'Category', 'MultiCategory', 'Currency'])->nullable(false);
        });

        Schema::create('user_crafting_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->unsignedInteger('recipe_id'); // The ID of the recipe from the recipes table
            $table->unsignedInteger('user_id')->nullable(); // Only one user should be involved in any one crafting attempt.

            $table->string('log', 500); // Actual log text.
            $table->string('log_type'); // Indicates success/failure/simple Craft. Here mostly to allow for expansion.
            
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_crafting_log');


        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropColumn('ingredient_type');
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->enum('ingredient_type', ['Item', 'MultiItem', 'Category', 'MultiCategory'])->nullable(false);
        });
    }
}
