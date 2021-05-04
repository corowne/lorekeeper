<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\UserService;
use App\Models\User\User;
use App\Models\Rank\Rank;

class SetupAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the admin user account if no users exist, or resets the password if it does.';

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
        $this->info('********************');
        $this->info('* ADMIN USER SETUP *');
        $this->info('********************'."\n");

        // First things first, check if user ranks exist... 
        if(!Rank::count()) {

            // These need to be created even if the seeder isn't run for the site to work correctly.
            $adminRank = Rank::create([
                'name' => 'Admin', 
                'description' => 'The site admin. Has the ability to view/edit any data on the site.',
                'sort' => 1
            ]);
            Rank::create([
                'name' => 'Member', 
                'description' => 'A regular member of the site.',
                'sort' => 0
            ]);

            $this->line("User ranks not found. Default user ranks (admin and basic member) created.");
        }
        // Otherwise, grab the rank with the highest "sort" value. (This is the admin rank.)
        else {
            $adminRank = Rank::orderBy('sort', 'DESC')->first();
        }

        // Check if the admin user exists...
        $user = User::where('rank_id', $adminRank->id)->first();
        if(!$user) {

            $this->line('Setting up admin account. This account will have access to all site data, please make sure to keep the email and password secret!');
            $name = $this->anticipate('Username', ['Admin', 'System']);
            $email = $this->ask('Email Address');
            $password = $this->secret('Password (hidden)');

            $this->line("\nUsername: ".$name);
            $this->line("Email: ".$email);
            $confirm = $this->confirm("Proceed to create account with this information?");

            if($confirm) {
                $service = new UserService;
                $service->createUser([
                    'name' => $name,
                    'email' => $email,
                    'rank_id' => $adminRank->id,
                    'password' => $password,
                    'dob' => [
                        'day' => '01',
                        'month' => '01',
                        'year' => '1971'
                    ],
                ]);

                $this->line('Admin account created. You can now log in with the registered email and password.');
                $this->line('If necessary, you can run this command again to change the email address and password of the admin account.');
                return;
            }
        }
        else {
            // Change the admin email/password. Honestly you can do this with the forgotten password feature...
            $this->line('Admin account [' . $user->name . '] already exists.');
            if($this->confirm("Reset email address and password for this account?")) {
                $email = $this->ask('Email Address');
                $password = $this->secret('Password (hidden)');

                $this->line("\nEmail: ".$email);
                if($this->confirm("Proceed to change email address and password?")) {
                    $service = new UserService;
                    $service->updateUser([
                        'id' => $user->id,
                        'email' => $email,
                        'password' => $password
                    ]);
                    
                    $this->line('Admin account email and password changed.');
                    return;
                }
            }
        }
        $this->line('Action cancelled.');
        
    }
}
