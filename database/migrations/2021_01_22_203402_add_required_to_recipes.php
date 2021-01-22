<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequiredToRecipes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            //
            $table->boolean('is_limited')->nullable()->default(null);
        });

        schema::create('recipe_limits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recipe_id');
            $table->string('limit_type');
            $table->integer('limit_id');
            $table->integer('quantity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            //
            $table->dropColumn('is_limited');
        });

        Schema::dropIfExists('recipe_limits');
    }
}
