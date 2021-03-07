<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class UpdateAliasSiteNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-alias-site-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates site names in user_aliases to use new keys.';

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
     * @return int
     */
    public function handle()
    {
        // This should be run at a point in time where all users have only dA aliases stored,
        // so we can simply change the entire table over to the new dA key.
        DB::table('user_aliases')->update(['site' => 'deviantart']);
        return 0;
    }
}
