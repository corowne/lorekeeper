<?php

namespace App\Console\Commands;

use App\Models\User\UserSettings;
use Illuminate\Console\Command;
use Settings;
use Config;
use App\Services\CurrencyManager;
use App\Models\User\UserCurrency;
use App\Models\User\User;
use DB;
use App\Models\Character\CharacterCurrency;
use App\Models\Character\Character;

class RefreshEncounterEnergy extends Command
{
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
    protected $description = 'Reset all user\'s or character\'s encounter energy for the day';

    /**
     * Create a new command instance.
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
        //oh boy i love 999 different if else checks

        //if energy regen set
        if (Config::get('lorekeeper.encounters.refresh_energy')) {
            $this->info('Refreshing encounter energy...');

            //if  characters are set
            if (Config::get('lorekeeper.encounters.use_characters')) {
                //if energy is set for characters
                if (Config::get('lorekeeper.encounters.use_energy')) {
                    Character::where('encounter_energy', '<', Settings::get('encounter_energy'))->update(['encounter_energy' => Settings::get('encounter_energy')]);
                } elseif (Config::get('lorekeeper.encounters.energy_replacement_id', '!=', 0)) {
                    //currency is set instead
                    //find character currencies
                    $characters = Character::all();
                    foreach ($characters as $character) {
                        $record = CharacterCurrency::where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))->first();
                        if ($record) {
                            //don't reset if the currency is above the setting. it's extremely likely that a currency will exceed the cap and we don't want it to dip back down.
                            if($record->quantity < Settings::get('encounter_energy')){
                                DB::table('character_currencies')
                                ->where('character_id', $character->id)
                                ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                                ->update(['quantity' => Settings::get('encounter_energy')]);
                            }
                        } else {
                            //else if no currency exists then make one
                            CharacterCurrency::create(['character_id' => $character->id, 'currency_id' => Config::get('lorekeeper.encounters.energy_replacement_id'), 'quantity' => Settings::get('encounter_energy')]);
                        }
                    }
                } else {
                    $this->error('The config\lorekeeper\encounters.php\'s currency settings weren\'t set right...');
                }
            } else {
                //if energy is set for users
                if (Config::get('lorekeeper.encounters.use_energy')) {
                    UserSettings::where('encounter_energy', '<', Settings::get('encounter_energy'))->update(['encounter_energy' => Settings::get('encounter_energy')]);
                } elseif (Config::get('lorekeeper.encounters.energy_replacement_id', '!=', 0)) {
                    //currency is set instead
                    //find user currencies
                    $users = User::all();
                    foreach ($users as $user) {
                        $record = UserCurrency::where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))->first();
                        if ($record) {
                            //don't reset if the currency is above the setting. it's extremely likely that a currency will exceed the cap and we don't want it to dip back down.
                            if($record->quantity < Settings::get('encounter_energy')){
                            DB::table('user_currencies')
                                ->where('user_id', $user->id)
                                ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                                ->update(['quantity' => Settings::get('encounter_energy')]);
                            }
                        } else {
                            //else if no currency exists then make one
                            UserCurrency::create(['user_id' => $user->id, 'currency_id' => Config::get('lorekeeper.encounters.energy_replacement_id'), 'quantity' => Settings::get('encounter_energy')]);
                        }
                    }
                } else {
                    $this->error('The config\lorekeeper\encounters.php\'s currency settings weren\'t set right...');
                }
            }
        } else {
            //energy regen not set
            $this->info('Energy isn\'t set to refresh. Change the config\lorekeeper\encounters.php file if this is incorrect.');
        }
    }
}
