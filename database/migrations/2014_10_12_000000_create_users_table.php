<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create user ranks and powers
        Schema::create('ranks', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('description', 512)->nullable();
            $table->integer('sort')->unsigned()->default(0);
            $table->string('color', 6)->nullable();
        });
        Schema::create('rank_powers', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('rank_id')->unsigned();
            $table->string('power')->index(); // Power IDs are defined in a config file

            $table->foreign('rank_id')->references('id')->on('ranks');
            $table->unique(['rank_id', 'power']);
        });

        // Create user tables
        // Running with the default Laravel users table here.
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->index()->unique();
            $table->string('alias')->nullable()->index();
            $table->integer('rank_id')->unsigned()->default(1);

            $table->integer('notifications_unread')->unsigned()->default(0);
            
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('rank_id')->references('id')->on('ranks');
        });
        Schema::create('user_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
            $table->boolean('is_fto')->default(1);
            $table->integer('character_count')->unsigned()->default(0);
            $table->integer('myo_slot_count')->unsigned()->default(0);

            $table->foreign('user_id')->references('id')->on('users');
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();

            $table->integer('notification_type_id')->unsigned(); // Notification type IDs are defined in a config file

            $table->boolean('is_unread')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
        
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('users');

        Schema::dropIfExists('rank_powers');
        Schema::dropIfExists('ranks');
    }
}
