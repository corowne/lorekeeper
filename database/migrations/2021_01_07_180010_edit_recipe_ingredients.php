<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditRecipeIngredients extends Migration
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
            $table->engine = 'InnoDB';
            $table->enum('ingredient_type', ['Item', 'MultiItem', 'Category', 'MultiCategory'])->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->dropColumn('ingredient_type');
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('ingredient_type', 32)->nullable(false);
        });
    }
}
