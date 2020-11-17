<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use App\Models\User\User;
use App\Models\User\UserAlias;
use App\Models\Character\Character;
use App\Models\Character\CharacterImageCreator;

class MigrateAliases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-aliases {--drop-columns : Whether the alias columns should be dropped after moving data from them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates alias information associated with users, characters, and character image creators to the new storage system.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** MOVE USER ALIASES */

        // Get users with a set alias
        $aliasUsers = User::whereNotNull('alias')->get();

        foreach($aliasUsers as $user) {
            if(!DB::table('user_aliases')->where('user_id', $user->id)->where('site', 'dA')->where('alias', $user->alias)->exists()) {
                // Create a new row for the user's current dA alias
                DB::table('user_aliases')->insert([
                    [
                        'user_id' => $user->id,
                        'site' => 'dA',
                        'alias' => $user->alias,
                        'is_visible' => 1,
                        'is_primary_alias' => 1,
                    ]
                ]);

                // Clear the user's alias in the users table and set the has_alias bool in its place
                $user->update([
                    'alias' => null,
                    'has_alias' => 1
                ]);
            }
        }

        /** MOVE CHARACTER OWNER ALIASES */
        // This and the following section operate on the assumption that all aliases to this point have been dA accounts

        // Get characters with an owner identified by alias
        $aliasCharacters = Character::whereNotNull('owner_alias')->get();
        
        foreach($aliasCharacters as $character) {
            // Just in case, check to update character ownership
            $userAlias = UserAlias::where('site', 'dA')->where('alias', $character->owner_alias)->first();
            if($userAlias) {
                $character->update(['owner_alias' => null, 'user_id' => $userAlias->user_id]);
            }
            elseif(!$userAlias) {
                $alias = $character->owner_alias;
                $character->update(['owner_alias' => null, 'owner_url' => 'https://deviantart.com/'.$alias]);
            }
        }
        
        /** MOVE CHARACTER IMAGE CREATOR ALIASES */

        // Get character image creators with a set alias
        $aliasImageCreators = CharacterImageCreator::whereNotNull('alias')->get();

        foreach($aliasImageCreators as $creator) {
            $userAlias = UserAlias::where('site', 'dA')->where('alias', $creator->alias)->first();
            if($userAlias) {
                $creator->update(['alias' => null, 'user_id' => $userAlias->user_id]);
            }
            elseif(!$userAlias) {
                $alias = $creator->alias;
                $creator->update(['alias' => null, 'url' => 'https://deviantart.com/'.$alias]);
            }
        }

        if($this->option('drop-columns')) {
            // Drop alias columns from the impacted tables.
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('alias');
            });
            Schema::table('characters', function (Blueprint $table) {
                $table->dropColumn('owner_alias');
            });
            Schema::table('character_image_creators', function (Blueprint $table) {
                $table->dropColumn('alias');
            });
        }
    }
}
