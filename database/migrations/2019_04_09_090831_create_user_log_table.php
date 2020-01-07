<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This will log any changes made to user accounts, mainly: 
        // 1. E-mail address changes (user side)
        // 2. Username changes (admin side)
        // 3. Alias clearing (admin side; logs old alias)
        // 4. Manual FTO status change (admin side)
        // This is so that
        // 1. Any changes to user account information made by a mod 
        //    can be attributed to a staff member. 
        //    (Possibly overkill for accountability, but...)
        // 2. If a blacklisted user tries to register, or a user tries to do
        //    something sketchy with multiple accounts, old logs
        //    might help confirm their identity.
        Schema::create('user_update_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->integer('staff_id')->unsigned()->nullable()->default(null);
            $table->integer('user_id')->unsigned();
            $table->string('type', 32);
            $table->string('data', 512);
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_update_log');
    }
}
