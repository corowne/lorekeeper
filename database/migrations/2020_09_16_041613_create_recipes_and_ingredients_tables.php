<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipesAndIngredientsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191)->nullable(false);
            $table->boolean('has_image')->default(false)->nullable(false);
            $table->boolean('needs_unlocking')->default(false)->nullable(false);
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
            $table->string('reference_url', 200)->nullable();
            $table->string('artist_alias', 191)->nullable();
            $table->string('artist_url', 191)->nullable();
        });

        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->unsignedInteger('recipe_id')->nullable(false);
            $table->string('ingredient_type', 32)->nullable(false);
            $table->string('ingredient_data', 1024)->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
        });

        Schema::create('recipe_rewards', function (Blueprint $table) {
            $table->unsignedInteger('recipe_id')->nullable(false);
            $table->string('rewardable_type', 32)->nullable(false);
            $table->unsignedInteger('rewardable_id')->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('recipe_rewards');
    }
}
