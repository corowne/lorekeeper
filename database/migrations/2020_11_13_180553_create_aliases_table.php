<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAliasesTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('user_aliases', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();

            $table->string('site')->index();
            $table->string('alias')->index();

            $table->boolean('is_visible')->default(0);
            $table->boolean('is_primary_alias')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('user_aliases');
    }
}
