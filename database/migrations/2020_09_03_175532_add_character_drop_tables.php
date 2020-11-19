<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharacterDropTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 
        Schema::create('character_drop_data', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('species_id')->unsigned();

            // Will hold defined parameters and item data.
            $table->text('parameters')->nullable()->default(null);
            $table->text('data')->nullable()->default(null);
        });

        Schema::create('character_drops', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            // Specific drop data being used, as well as associated character
            $table->integer('drop_id')->unsigned();
            $table->integer('character_id')->unsigned();

            // Specific parameters associated with the individual character
            $table->text('parameters')->nullable();

            // Number of opportunities to collect the drops. Not equivalent to quantity.
            $table->integer('drops_available')->unsigned()->default(0);

            // Timestamp at which next drop becomes available
            $table->timestamp('next_day')->nullable()->default(null);

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
        Schema::dropIfExists('character_drop_data');
        Schema::dropIfExists('character_drops');
    }
}
