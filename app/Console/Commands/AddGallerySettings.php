<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class AddGallerySettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-gallery-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds gallery-related settings.';

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
        $this->info('*********************');
        $this->info('* ADD GALLERY SETTINGS *');
        $this->info('*********************'."\n");

        $this->line("Adding gallery-related settings...existing entries will be skipped.\n");

        if(!DB::table('site_settings')->where('key', 'gallery_submissions_open')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'gallery_submissions_open',
                    'value' => 1,
                    'description' => '0: Gallery submissions closed, 1: Gallery submissions open.'
                ]

            ]);
            $this->info("Added:   gallery_submissions_open / Default: 1");
        }
        else $this->line("Skipped: gallery_submissions_open");
        
        if(!DB::table('site_settings')->where('key', 'gallery_submissions_require_approval')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'gallery_submissions_require_approval',
                    'value' => 1,
                    'description' => '0: Gallery submissions do not require approval, 1: Gallery submissions require approval.'
                ]

            ]);
            $this->info("Added:   gallery_submissions_require_approval / Default: 1");
        }
        else $this->line("Skipped: gallery_submissions_require_approval");

        if(!DB::table('site_settings')->where('key', 'gallery_submissions_reward_currency')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'gallery_submissions_reward_currency',
                    'value' => 0,
                    'description' => '0: Gallery submissions do not reward currency, 1: Gallery submissions reward currency.'
                ]

            ]);
            $this->info("Added:   gallery_submissions_reward_currency / Default: 0");
        }
        else $this->line("Skipped: gallery_submissions_reward_currency");

        if(!DB::table('site_settings')->where('key', 'group_currency')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'group_currency',
                    'value' => 1,
                    'description' => 'ID of the group currency to award from gallery submissions (if enabled).'
                ]

            ]);
            $this->info("Added:   group_currency / Default: 1");
        }
        else $this->line("Skipped: group_currency");

        $this->line("\nSite settings up to date!");
        
    }
}
