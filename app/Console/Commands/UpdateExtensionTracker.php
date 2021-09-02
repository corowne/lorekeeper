<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Config;

class UpdateExtensionTracker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-extension-tracker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run this command in order to add extensions to your list of extensions. This helps you to keep track of what extensions are used on your site and who created them.';

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
        $extensions = Config::get('lorekeeper.extension_tracker');

        $this->info("\n".'****************************');
        $this->info('* UPDATE EXTENSION TRACKER *');
        $this->info('****************************');

        $this->line('Adding site extensions...existing entries will be updated.'."\n");

        foreach($extensions as $data)
        {
            $extension = DB::table('site_extensions')->where('key', $data['key']);
            if(!$extension->exists())
            {
                DB::table('site_extensions')->insert([
                    'key' => $data['key'],
                    'wiki_key' => $data['wiki_key'],
                    'creators' => $data['creators'],
                    'version' => $data['version'],
                ]);
                $this->info('Added:   '.$data['key'].' / Version: '.$data['version']);
            }
            elseif($extension->first()->version != $data['version'])
            {
                $this->info(ucfirst($data['key']).' version mismatch. Old version: '.$extension->first()->version.' / New version: '.$data['version']);
                $confirm = $this->confirm('Do you want to update the listed version of '.$data['key'].' to '.$data['version'].'? This will not affect any other files.');
                if($confirm){
                    DB::table('site_extensions')->where('key', $data['key'])->update([
                        'key' => $data['key'],
                        'wiki_key' => $data['wiki_key'],
                        'creators' => $data['creators'],
                        'version' => $data['version'],
                    ]);
                    $this->info('Updated:   '.$data['key'].' / Version: '.$data['version']);
                }
                else $this->line('Skipped: '.$data['key'].' / Version: '.$extension->first()->version);
            }
            else $this->line('Skipped: '.$data['key'].' / Version: '.$data['version']);
        }
        
        $this->info("\n".'All extensions are in tracker.'."\n");

    }
}
