<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->boolean('is_user_owned')->default(0);
            $table->boolean('is_character_owned')->default(0);

            $table->string('name');
            $table->string('abbreviation')->nullable()->default(null); // optional short form for the name
            $table->string('description', 512)->nullable()->default(null);
            $table->integer('sort_user')->unsigned()->default(0); // larger shows up first
            $table->integer('sort_character')->unsigned()->default(0); // larger shows up first

            // Applicable only to users. 
            // Chooses whether to show on the user profile,
            // or if it's only visible from the bank page.
            // This allows event-only currencies to be created for short-term use
            // but not clutter up user profiles. 
            // (Non-displayed currencies are not listed if the user doesn't own any,
            // but displayed ones will display as 0.)
            $table->boolean('is_displayed')->default(1);

            // Transfers are tricky - there are cases where spare "points" are assigned
            // to the user to distribute freely among their characters.
            // Theoretically, a game could allow points to be transferred from
            // characters to users/other characters...but to keep things simple,
            // user/character transfers are between the owner and their own characters.
            // (I imagine character-to-user transfers are unusual, but it might just be needed...)
            $table->boolean('allow_user_to_user')->default(0);
            $table->boolean('allow_user_to_character')->default(0);
            $table->boolean('allow_character_to_user')->default(0);

            $table->boolean('has_icon')->default(0); // if an icon exists, it'll be displayed in place of the name/abbrev.
            $table->boolean('has_image')->default(0); // for showing on the world page. Also optional.
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('character_id')->unsigned()->nullable();
            $table->integer('currency_id')->unsigned();

            $table->integer('quantity')->default(0);
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('character_id')->references('id')->on('characters');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
        
        Schema::create('banks_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('sender_id')->unsigned()->nullable()->default(null);
            $table->enum('sender_type', ['User', 'Character'])->nullable()->default(null);
            $table->integer('recipient_id')->unsigned()->nullable()->default(null);
            $table->enum('recipient_type', ['User', 'Character'])->nullable()->default(null);

            // 1. Staff grant (manual)
            // 2. Staff removal (manual)
            // 3. Transferred to other user
            // 4. Transferred to character
            // 5. Taken from character
            // 6. Activity reward
            // ...and more
            $table->string('type', 32);

            // Any additional data, e.g. a reason for a staff grant, name of rewarded activity, etc.
            $table->string('data', 512);
            
            $table->integer('currency_id')->unsigned();
            $table->integer('quantity')->default(0);

            $table->timestamps();
            
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks_log');
        Schema::dropIfExists('banks');
        Schema::dropIfExists('currencies');
    }
}
