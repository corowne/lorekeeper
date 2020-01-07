<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiteSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 
        Schema::create('site_settings', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('key', 50)->unique()->primary();
            $table->string('value');
            $table->string('description', 1024);
        });
        
        Schema::create('site_pages', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('key', 30)->unique();
            $table->string('title', 50);
            $table->text('text')->nullable();
            $table->boolean('is_listed')->default(1); // to list on the page directory
            $table->boolean('is_visible')->default(1); // visibility to users without editing powers
            $table->timestamps(); // for showing a "last edited" date
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
        Schema::dropIfExists('site_pages');
        Schema::dropIfExists('site_settings');
    }
}
