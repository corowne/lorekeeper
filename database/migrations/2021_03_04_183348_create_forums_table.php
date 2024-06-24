<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name', 191);

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->boolean('layout')->default(0); // 0 = Flat, 1 = Threaded
            $table->boolean('is_active')->default(1);
            $table->boolean('is_locked')->default(0);
            $table->boolean('staff_only')->default(0);

            $table->integer('sort')->unsigned()->default(0);

            $table->integer('role_limit')->unsigned()->nullable()->default(null);
            $table->integer('parent_id')->unsigned()->nullable()->default(null);

            $table->boolean('has_image')->default(0);
            $table->string('extension', 191)->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();

        });
        Schema::table('comments', function (Blueprint $table) {
            $table->string('title', 191)->nullable()->default(null);
            $table->boolean('is_locked')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('is_locked');
        });
        Schema::dropIfExists('forums');
    }
}
