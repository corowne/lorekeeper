<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        if (!Schema::hasTable('character_image_subtypes')) {
            $this->call('convert-character-subtype');
        }

        // check call was successful
        if (!Schema::hasTable('character_image_subtypes')) {
            throw new \Exception('The character_image_subtypes table does not exist.');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        throw new \Exception('This migration cannot be reversed.');
    }
};
