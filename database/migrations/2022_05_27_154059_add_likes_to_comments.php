<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLikesToComments extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comment_likes', function (Blueprint $table) {
            //
            $table->integer('user_id')->unsigned();
            $table->integer('comment_id')->unsigned();
            $table->boolean('is_like')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('comment_likes');
    }
}
