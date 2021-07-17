<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesCharacters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_characters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('sales_id')->unsigned()->index();
            $table->integer('character_id')->unsigned()->index();
            // Optional description area
            $table->text('description')->nullable()->default(null);

            // Columns for character sale type and pricing data
            $table->string('type');
            $table->string('data')->nullable()->default(null);

            $table->boolean('is_open')->default(1);
            $table->string('link')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_characters');
    }
}
