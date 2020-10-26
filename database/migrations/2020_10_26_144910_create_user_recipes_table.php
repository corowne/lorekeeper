<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

            // The sender_id, if granted by an admin, is the admin user's id.
            // Recipes shouldn't be user-user transferrable, so if it's null then it's implied that it's purchased.
            // Recipes should always belong to a user, but they can be purchased using character currency, ergo character_id.
            $table->unsignedInteger('sender_id')->nullable(); 
            $table->unsignedInteger('recipient_id'); 
            $table->unsignedInteger('character_id')->nullable(); 
            
            $table->timestamps();
        });

        // Schema::create('user_crafting_log', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->increments('id');

        //     $table->unsignedInteger('recipe_id'); // The ID of the recipe from the recipes table

        //     // The sender_id, if granted by an admin, is the admin user's id.
        //     // Recipes shouldn't be user-user transferrable, so if it's null then it's implied that it's purchased.
        //     // Recipes should always belong to a user, but they can be purchased using character currency, ergo character_id.
        //     $table->unsignedInteger('sender_id')->nullable(); 
        //     $table->unsignedInteger('recipient_id'); 
        //     $table->unsignedInteger('character_id')->nullable(); 
            
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_recipes_log');
        Schema::dropIfExists('user_recipes');
    }
}
