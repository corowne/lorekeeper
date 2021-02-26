<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPageCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Page categories are for displaying pages on the World/Encyclopedia page
        Schema::create('site_page_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('name');
            $table->boolean('has_image')->default(0);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->integer('sort')->unsigned()->default(0);
            });

        //link site_pages to page_categories
        Schema::table('site_pages', function(Blueprint $table) {
            $table->integer('page_category_id')->unsigned()->nullable()->default(null);
            // Null means they won't be displayed on the World section
            $table->foreign('page_category_id')->references('id')->on('site_page_categories');
            });
    }
        
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_page_categories');

        Schema::table('site_pages', function(Blueprint $table) {
            $table->dropColumn('page_category_id');
        });
    }
}