<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\Models\User\User;
use App\Models\User\UserAlias;

class MoveUserAliases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move-user-aliases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moves existing user aliases from the users table to the user_aliases table.';

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
    }
}
