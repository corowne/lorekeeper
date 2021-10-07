<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCraftingSystemTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 191)->nullable(false);
            $table->boolean('has_image')->default(false)->nullable(false);
            $table->boolean('needs_unlocking')->default(false)->nullable(false);
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
            $table->string('reference_url', 200)->nullable();
            $table->string('artist_alias', 191)->nullable();
            $table->string('artist_url', 191)->nullable();
            $table->text('output')->nullable()->default(null);
            $table->boolean('is_limited')->nullable()->default(null);
        });

        Schema::create('recipe_limits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recipe_id');
            $table->string('limit_type');
            $table->integer('limit_id');
            $table->integer('quantity')->default(1);
        });

        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('recipe_id')->nullable(false);
            $table->enum('ingredient_type', ['Item', 'MultiItem', 'Category', 'MultiCategory', 'Currency'])->nullable(false);
            $table->string('ingredient_data', 1024)->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
        });

        Schema::create('recipe_rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('recipe_id')->nullable(false);
            $table->string('rewardable_type', 32)->nullable(false);
            $table->unsignedInteger('rewardable_id')->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
        });

        Schema::create('user_recipes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('recipe_id'); //
            $table->timestamps();
        });

        Schema::create('user_recipes_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->unsignedInteger('recipe_id'); // The ID of the recipe from the recipes table

            $table->string('log', 500); // Actual log text
            $table->string('log_type'); // Indicates how the recipe was received.
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            // The sender_id, if granted by an admin, is the admin user's id.
            // Recipes shouldn't be user-user transferrable, so if it's null then it's implied that it's purchased.
            // Recipes should always belong to a user, but they can be purchased using character currency, ergo character_id
            $table->unsignedInteger('sender_id')->nullable(); 
            $table->unsignedInteger('recipient_id')->nullable(); // Nullable in the case that a recipe has to be rescinded for whatever reason
            $table->unsignedInteger('character_id')->nullable(); 
            
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
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('recipe_limits');
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('recipe_rewards');
        Schema::dropIfExists('user_recipes_log');
        Schema::dropIfExists('user_recipes');
    }
}
