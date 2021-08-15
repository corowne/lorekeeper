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
use App\Models\Stats\Character\CharacterLevel;
use App\Models\Stats\User\UserLevel;

use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Prompt\Prompt;

use App\Models\User\UserItem;
use App\Models\User\UserCurrency;

use App\Models\Character\CharacterItem;
use App\Models\Character\CharacterCurrency;

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
                throw new \Exception('Error debiting exp.');
            }

            // give stat points
            $service = new StatManager;
            if($next->stat_points != 0)
            {
                if(!$service->creditUserStat($user, 'Level Up Reward', 'Rewards for levelling up to .'.$next->level.'.', $next))
                {
                    throw new \Exception('Error granting stat points.');
                }
            }
            ////////////////////////////////////////////////////// LEVEL REWARDS
            $levelRewards = createAssetsArray();

                foreach($next->rewards as $reward)
                {
                    addAsset($levelRewards, $reward->reward, $reward->quantity);
                }

            // Logging data
            $levelLogType = 'Level Rewards';
            $levelData = [
                'data' => 'Received rewards for level up to level '.$next->level.'.'
            ];

            // Distribute user rewards
            if(!$levelRewards = fillUserAssets($levelRewards, null, $user, $levelLogType, $levelData)) throw new \Exception("Failed to distribute rewards to user.");
            /////////////////////////////////////////////////

            foreach($next->limits as $limit)
            {
                $rewardType = $limit->rewardable_type;
                $check = NULL;
                switch($rewardType)
                {
                    case 'Item':
                        $check = UserItem::where('item_id', $limit->reward->id)->where('user_id', auth::user()->id)->where('count', '>=', $limit->quantity)->first();
                        break;
                    case 'Currency':
                        $check = UserCurrency::where('currency_id', $limit->reward->id)->where('user_id', auth::user()->id)->where('quantity', '>=', $limit->quantity)->first();
                        break;
                    //case 'Recipe':
                    //    $check = UserRecipe::where('recipe_id', $limit->reward->id)->where('user_id', auth::user()->id)->first();
                    //    break;
                }

                if(!$check) throw new \Exception('You require ' . $limit->reward->name . ' x ' . $limit->quantity . ' to level up.');
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

    public function characterLevel($character)
    {        
        DB::beginTransaction();

        try {

            $service = new ExperienceManager;

            $level = $character->level;

            // getting the next level
            $check = $character->level->current_level + 1;
            $next = CharacterLevel::where('level', $check)->first();

            // validation
            if(!$next) throw new \Exception('You are at the max level!');
            if($character->level->current_exp < $next->required_exp) throw new \Exception('You do not have enough exp to level up!');

            if(!$service->debitExp($character, 'Level Up', 'Used EXP in level up.', $level, $next->exp_required))
            {
                throw new \Exception('Error debiting exp.');
            }

            // give stat points
            $service = new StatManager;
            if($next->stat_points != 0)
            {
                if(!$service->creditCharaStat($character, 'Level Up Reward', 'Rewards for levelling up.', $next))
                {
                    throw new \Exception('Error granting stat points.');
                }
            }

            ////////////////////////////////////////////////////// LEVEL REWARDS
            $levelRewards = createAssetsArray();

                foreach($next->rewards as $reward)
                {
                    addAsset($levelRewards, $reward->reward, $reward->quantity);
                }

            // Logging data
            $levelLogType = 'Level Rewards';
            $levelData = [
                'data' => 'Received rewards for level up to level '.$next->level.'.'
            ];

            // Distribute user rewards
            if(!$levelRewards = fillCharacterAssets($levelRewards, null, $character, $levelLogType, $levelData)) throw new \Exception("Failed to distribute rewards to user.");
            /////////////////////////////////////////////////

            foreach($next->limits as $limit)
            {
                $rewardType = $limit->rewardable_type;
                $check = NULL;
                switch($rewardType)
                {
                    case 'Item':
                        $check = CharacterItem::where('item_id', $limit->reward->id)->where('character_id', $character->id)->where('count', '>', 0)->first();
                        break;
                    case 'Currency':
                        $check = CharacterCurrency::where('currency_id', $limit->reward->id)->where('character_id', $character->id)->where('count', '>', 0)->first();
                        break;
                    //case 'Recipe':
                    //    $check = UserRecipe::where('recipe_id', $limit->reward->id)->where('user_id', auth::user()->id)->first();
                    //    break;
                }

                if(!$check) throw new \Exception('You require ' . $limit->reward->name . ' to level up.');
            }

            // create log
            if($this->createlog($character, 'Character', $character->level->current_level, $next->level)) {
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

     /**
     * Processes reward data into a format that can be used for distribution.
     *
     * @param  array $data
     * @param  bool  $isCharacter
     * @param  bool  $isStaff
     * @return array
     */
    private function processRewards($data, $isCharacter, $isStaff = false)
    {
        if($isCharacter)
        {
            $assets = createAssetsArray(true);
            if(isset($data['character_currency_id'][$data['character_id']]) && isset($data['character_quantity'][$data['character_id']]))
            {
                foreach($data['character_currency_id'][$data['character_id']] as $key => $currency)
                {
                    if($data['character_quantity'][$data['character_id']][$key]) addAsset($assets, $data['currencies'][$currency], $data['character_quantity'][$data['character_id']][$key]);
                }
            }
            return $assets;
        }

        $assets = createAssetsArray(false);
        // Process the additional rewards
        if(isset($data['rewardable_type']) && $data['rewardable_type'])
        {
            foreach($data['rewardable_type'] as $key => $type)
            {
                $reward = null;
                switch($type)
                {
                    case 'Item':
                        $reward = Item::find($data['rewardable_id'][$key]);
                        break;
                    case 'Currency':
                        $reward = Currency::find($data['rewardable_id'][$key]);
                        if(!$reward->is_user_owned) throw new \Exception("Invalid currency selected.");
                        break;
                    case 'LootTable':
                        if (!$isStaff) break;
                        $reward = LootTable::find($data['rewardable_id'][$key]);
                        break;
                    case 'Raffle':
                        if (!$isStaff) break;
                        $reward = Raffle::find($data['rewardable_id'][$key]);
                        break;
                }
                if(!$reward) continue;
                addAsset($assets, $reward, $data['quantity'][$key]);
            }
        }
        return $assets;
    }

    private function processData($levelRewards)
    {
        $rewards = [];
        foreach($levelRewards as $type => $a)
        {
            $class = getAssetModelString($type, false);
            foreach($a as $id => $asset)
            {
                $rewards[] = (object)[
                    'rewardable_type' => $class,
                    'rewardable_id' => $id,
                    'quantity' => $asset['quantity']
                ];
            }
        }
        return $rewards;
    }
}