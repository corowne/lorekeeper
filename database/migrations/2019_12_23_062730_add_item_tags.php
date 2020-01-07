<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table tags items with a tag type and data.
        // Handlers can be implemented for different tag types.
        Schema::create('item_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('item_id')->unsigned();
            $table->string('tag')->index();

            // This will hold the data required for using/displaying this item.
            // Note that the forms for editing the item will also have 
            // to be created yourself.
            $table->text('data')->nullable()->default(null);

            // This toggle allows you to disable the item tag,
            // e.g. in a situation where you want an item to be usable
            // during an event, but not afterwards and you want to 
            // preserve the settings so you can reuse it again later
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('item_tags');
    }
}
