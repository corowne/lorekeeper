<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        //snatching this flag from newt, less overlap means more Good
        //this is assuming that people have pulled and migrated claymores first-- which at the moment is very likely, but it isn't 100% foolproof as people with newer LKs arise...
        //the solution would be pretty simple in removing the 'is_focus' migration if a duplicate column error occurs
        //alas i cannot prevent every possible error, merely mitigate the damage as much as i can :pensive:
        if (!Schema::hasColumn('submission_characters', 'is_focus')) {
            Schema::table('submission_characters', function (Blueprint $table) {
                $table->boolean('is_focus')->default(0);
            });
        }
        Schema::table('prompt_rewards', function (Blueprint $table) {
            $table->string('earner_type')->default('User');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('submission_characters', function (Blueprint $table) {
            $table->dropColumn('is_focus');
        });
        Schema::table('prompt_rewards', function (Blueprint $table) {
            $table->dropColumn('earner_type');
        });
    }
};
