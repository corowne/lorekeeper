<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('commenter_id')->nullable();
            $table->string('commenter_type')->nullable();
            $table->index(['commenter_id', 'commenter_type']);

            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            $table->string('commentable_type');
            $table->string('commentable_id');
            $table->index(['commentable_type', 'commentable_id']);

            $table->text('comment');

            $table->boolean('approved')->default(true);

            $table->unsignedBigInteger('child_id')->nullable();
            $table->foreign('child_id')->references('id')->on('comments')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
