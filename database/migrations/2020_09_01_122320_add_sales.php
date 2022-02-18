<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSales extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::create('sales', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();

            $table->string('title');
            $table->text('text');
            $table->text('parsed_text');

            $table->boolean('is_visible')->default(1);

            $table->timestamps();
            $table->timestamp('post_at')->nullable()->default(null);

            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_sales_unread')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_sales_unread');
        });
        Schema::dropIfExists('sales');
    }
}
