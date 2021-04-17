<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
use DB;
use Carbon\Carbon;

class AddDonationShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-donation-shop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds information for the donation shop.';

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
        $this->line("Adding donation shop text...\n");
        $text = '<p>This is the shop text for the Donation Shop! It can be edited from the site pages admin panel.</p>
        <p>Items in this shop are donated by users of this site and can be collected at no cost.</p>';

        if(!DB::table('site_pages')->where('key', 'donation-shop')->exists()) {
            DB::table('site_pages')->insert([
                [
                    'key' => 'donation-shop',
                    'title' => 'Donation Shop',
                    'text' => $text,
                    'parsed_text' => $text,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'is_visible' => 0,
                ]

            ]);
            $this->info("Added: Donation Shop Text");
        }
        else $this->line("Skipped: Donation Shop Text");
    }
}
