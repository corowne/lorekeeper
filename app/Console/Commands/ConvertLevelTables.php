<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Schema;

class ConvertLevelTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert-level-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Combines level_users and level_characters for efficiency';

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
        $characterLevels = DB::table('level_characters')->get();
        // $userLevels = \DB::table('level_users')->get();

        // run the migration
        $this->info('Running migration...');
        $this->call('migrate');


        foreach($characterLevels as $characterLevel)
        {
            $userLevel = new \App\Models\Level\Level();
            $userLevel->level = $characterLevel->level;
            $userLevel->exp_required = $characterLevel->exp_required;
            $userLevel->stat_points = $characterLevel->stat_points;
            $userLevel->description = $characterLevel->description;
            $userLevel->level_type = 'Character';
            $userLevel->save();
        }

        // drop the old table
        $this->info('Dropping old table...');
        Schema::dropIfExists('level_characters');
        // print done
        $this->info('Done');
    }
}
