<?php namespace App\Services\Stats;

use Carbon\Carbon;
use App\Services\Service;

use DB;
use Auth;
use Config;
use Notifications;

use App\Models\Stats\User\Level;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Stats\Character\CharaLevels;
use App\Models\Stats\User\UserLevel;

class LevelManager extends Service
{
    public function userLevel($user)
    {        
        DB::beginTransaction();

        try {

            $service = new ExperienceManager;

            $level = $user->level;

            // getting the next level
            $check = $user->level->current_level + 1;
            $next = Level::where('level', $check)->first();

            // validation
            if(!$next) throw new \Exception('You are at the max level!');
            if($user->level->current_exp < $next->required_exp) throw new \Exception('You do not have enough exp to level up!');

            if(!$service->debitExp($user, 'Level Up', 'Used EXP in level up.', $level, $next->exp_required))
            {
                throw new \Exception('Error debitting exp.');
            }

            // give stat points
            $service = new StatManager;

            if(!$service->creditUserStat($user, 'Level Up Reward', 'Rewards for levelling up.', $next))
            {
                throw new \Exception('Error granting stat points.');
            }

            // create log
            if($this->createlog($user, 'User', $user->level->current_level, $next->level)) {
                $level->current_level += 1;
                $level->save();
            }
            else {
                throw new \Exception('Could not create log :(');
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Creates a log.
     */
    public function createLog($user, $recipientType, $currentLevel, $newLevel)
    {
        
        return DB::table('level_log')->insert(
            [
                'recipient_id' => $user->id,
                'leveller_type' => $recipientType,
                'previous_level' => $currentLevel,
                'new_level' => $newLevel,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}