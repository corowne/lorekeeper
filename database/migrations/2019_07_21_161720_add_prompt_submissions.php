<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromptSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('submissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('prompt_id')->unsigned()->index();

            $table->integer('user_id')->unsigned()->index();
            $table->integer('staff_id')->unsigned()->nullable()->default(null);

            $table->string('url', 200);
            
            $table->text('comments')->nullable()->default(null);
            $table->text('staff_comments')->nullable()->default(null);

            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');

            $table->string('data', 512)->nullable()->default(null);

            $table->timestamps();
        });
        Schema::create('submission_characters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('submission_id')->unsigned()->index();
            $table->integer('character_id')->unsigned()->index();

            $table->string('data', 512)->nullable()->default(null);
        });
        
        Schema::create('claims', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->index();
            $table->integer('staff_id')->unsigned()->nullable()->default(null)->index();

            $table->string('url');
            $table->text('comments')->nullable()->default(null);
            $table->text('staff_comments')->nullable()->default(null);

            $table->string('data', 512)->nullable()->default(null);

            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            
            $table->timestamps();
        });
        Schema::create('claim_characters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('claim_id')->unsigned()->index();
            $table->integer('character_id')->unsigned()->index();

            $table->string('data', 512)->nullable()->default(null);
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
        Schema::dropIfExists('claim_characters');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('submission_characters');
        Schema::dropIfExists('submissions');
    }
}
