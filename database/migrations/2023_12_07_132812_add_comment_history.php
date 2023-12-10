<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentHistory extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        // so if we remigrate it doesnt error
        if (!Schema::hasColumn('comments', 'updated_at')) {
            Schema::table('comment_likes', function (Blueprint $table) {
                // changed tables
                $table->unsignedBigInteger('comment_id')->change();

                // add foreign keys
                $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        Schema::create('comment_edits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->integer('user_id')->unsigned();
            $table->text('data');
            $table->timestamps();

            // add foreign keys
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        // comment likes cannot be reversed since its a bugfix
        Schema::dropIfExists('comment_edits');
    }
}
