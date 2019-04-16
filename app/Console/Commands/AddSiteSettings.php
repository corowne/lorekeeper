<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class AddSiteSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-site-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the default site settings.';

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
        if(!DB::table('site_settings')->where('key', 'is_registration_open')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'is_registration_open',
                    'value' => 1,
                    'description' => '0: Registration closed, 1: Registration open. When registration is closed, invitation keys can still be used to register.'
                ]

            ]);
            $this->line("Added: is_registration_open / Default: 1");
        }
        else $this->line("Skipped: is_registration_open");
        
    }
}
