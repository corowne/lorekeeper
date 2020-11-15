<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Character\CharacterImageCreator;

class AssignArtCreditsToIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign-art-credits-to-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns existing art and design credits on character images to user IDs if possible.';

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
        // Get character image creators with a set alias
        $aliasImageCreators = CharacterImageCreator::whereNotNull('alias')->get();

        foreach($aliasImageCreators as $creator) {
            $user = User::where('alias', $creator->alias)->first();
            if($user) {
                $creator->update(['alias' => null, 'user_id' => $user->id]);
            }
            elseif(!$user) {
                $alias = $creator->alias;
                $creator->update(['alias' => null, 'url' => 'https://deviantart.com/'.$alias]);
            }
        }
    }
}
