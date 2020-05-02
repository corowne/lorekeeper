<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\User\User;

class DropCharacterMyoCounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This count is not being used anywhere and is inaccurate
        // depending on your viewing permissions
        Schema::table('user_settings', function(Blueprint $table) {
            $table->dropColumn('character_count');
            $table->dropColumn('myo_slot_count');
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
        Schema::table('user_settings', function(Blueprint $table) {
            $table->integer('character_count')->unsigned()->default(0);
            $table->integer('myo_slot_count')->unsigned()->default(0);
        });

        $users = User::all();
        foreach($users as $user) {
            $user->settings->character_count = $user->characters->count();
            $user_settings->myo_slot_count = $user->myoSlots->count();
            $user->settings->save();
        }
    }
}
