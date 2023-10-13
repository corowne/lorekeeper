<?php

namespace App\Console\Commands;

use App\Models\User\UserSettings;
use Illuminate\Console\Command;
use Settings;

class RefreshEncounterEnergy extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh-encounter-energy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all user\'s encounter energy for the day';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        UserSettings::where('encounter_energy', '<', Settings::get('encounter_energy'))->update(['encounter_energy' => Settings::get('encounter_energy')]);
    }
}
