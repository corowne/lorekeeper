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
        $this->line("Adding site settings...existing entries will be skipped.\n");

        if(!DB::table('site_settings')->where('key', 'is_registration_open')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'is_registration_open',
                    'value' => 1,
                    'description' => '0: Registration closed, 1: Registration open. When registration is closed, invitation keys can still be used to register.'
                ]

            ]);
            $this->info("Added:   is_registration_open / Default: 1");
        }
        else $this->line("Skipped: is_registration_open");
        
        if(!DB::table('site_settings')->where('key', 'transfer_cooldown')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'transfer_cooldown',
                    'value' => 0,
                    'description' => 'Number of days to add to the cooldown timer when a character is transferred.'
                ]

            ]);
            $this->info("Added:   transfer_cooldown / Default: 0");
        }
        else $this->line("Skipped: transfer_cooldown");
        
        if(!DB::table('site_settings')->where('key', 'open_transfers_queue')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'open_transfers_queue',
                    'value' => 0,
                    'description' => '0: Character transfers do not need mod approval, 1: Transfers must be approved by a mod.'
                ]

            ]);
            $this->info("Added:   open_transfers_queue / Default: 0");
        }
        else $this->line("Skipped: open_transfers_queue");
        
        if(!DB::table('site_settings')->where('key', 'is_prompts_open')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'is_prompts_open',
                    'value' => 1,
                    'description' => '0: New prompt submissions cannot be made (mods can work on the queue still), 1: Prompts are submittable.'
                ]

            ]);
            $this->info("Added:   is_prompts_open / Default: 1");
        }
        else $this->line("Skipped: is_prompts_open");
        
        if(!DB::table('site_settings')->where('key', 'is_claims_open')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'is_claims_open',
                    'value' => 1,
                    'description' => '0: New claims cannot be made (mods can work on the queue still), 1: Claims are submittable.'
                ]

            ]);
            $this->info("Added:   is_claims_open / Default: 1");
        }
        else $this->line("Skipped: is_claims_open");
        
        if(!DB::table('site_settings')->where('key', 'is_myos_open')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'is_myos_open',
                    'value' => 1,
                    'description' => '0: MYO slots cannot be submitted for design approval, 1: MYO slots can be submitted for approval.'
                ]

            ]);
            $this->info("Added:   is_myos_open / Default: 1");
        }
        else $this->line("Skipped: is_myos_open");
        
        if(!DB::table('site_settings')->where('key', 'is_design_updates_open')->exists()) {
            DB::table('site_settings')->insert([
                [
                    'key' => 'is_design_updates_open',
                    'value' => 1,
                    'description' => '0: Characters cannot be submitted for design update approval, 1: Characters can be submitted for design update approval.'
                ]

            ]);
            $this->info("Added:   is_design_updates_open / Default: 1");
        }
        else $this->line("Skipped: is_design_updates_open");

        $this->line("\nSite settings up to date!");
        
    }
}
