<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRewardStatPrompt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        schema::table('prompts', function (Blueprint $table) {
            $table->string('user_exp')->nullable()->default(null);
            $table->string('user_points')->nullable()->default(null);
            $table->string('chara_exp')->nullable()->default(null);
            $table->string('chara_points')->nullable()->default(null);
            $table->integer('level_req')->unsigned()->nullable()->default(null);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('focus_chara_id')->nullable()->default(null);
            $table->string('bonus')->nullable()->default(null);
        });

        Schema::create('count_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('quantity')->default(1);

            $table->integer('sender_id')->unsigned()->nullable();
            $table->integer('character_id')->unsigned()->nullable();
            
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Indicates what type of transaction the item was used in
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            $table->timestamps();

            //Add sender and recipient type. Set default user to account for preexisting rows
            $table->enum('sender_type', ['User', 'Character'])->nullable()->default('User');
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
        schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn('user_exp');
            $table->dropColumn('user_points');
            $table->dropColumn('chara_exp');
            $table->dropColumn('chara_points');
            $table->dropColumn('level_req');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('focus_chara_id');
            $table->dropColumn('bonus');
        });

        Schema::dropIfExists('count_log');
    }
}
