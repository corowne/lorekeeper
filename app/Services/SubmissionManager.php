<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;
use Config;
use Image;
use Notifications;
use Settings;

use Illuminate\Support\Arr;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Character\Character;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Prompt\Prompt;

use App\Services\Stats\ExperienceManager;
use App\Services\Stats\StatManager;

class SubmissionManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Submission Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of submission data.
    |
    */

    /**
     * Creates a new submission.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @param  bool                   $isClaim
     * @return mixed
     */
    public function createSubmission($data, $user, $isClaim = false)
    {
        DB::beginTransaction();

        try {
            // 1. check that the prompt can be submitted at this time
            // 2. check that the characters selected exist (are visible too)
            // 3. check that the currencies selected can be attached to characters
            if(!$isClaim && !Settings::get('is_prompts_open')) throw new \Exception("The prompt queue is closed for submissions.");
            else if($isClaim && !Settings::get('is_claims_open')) throw new \Exception("The claim queue is closed for submissions.");
            if(!$isClaim && !isset($data['prompt_id'])) throw new \Exception("Please select a prompt.");
            if(!$isClaim) {
                $prompt = Prompt::active()->where('id', $data['prompt_id'])->with('rewards')->first();
                if(!$prompt) throw new \Exception("Invalid prompt selected.");
            }
            else $prompt = null;

            // The character identification comes in both the slug field and as character IDs
            // that key the reward ID/quantity arrays.
            // We'll need to match characters to the rewards for them.
            // First, check if the characters are accessible to begin with.
            if(isset($data['slug'])) {
                $characters = Character::myo(0)->visible()->whereIn('slug', $data['slug'])->get();
                if(count($characters) != count($data['slug'])) throw new \Exception("One or more of the selected characters do not exist.");
            }
            else $characters = [];

            $userAssets = createAssetsArray();

            // Attach items. Technically, the user doesn't lose ownership of the item - we're just adding an additional holding field.
            // We're also not going to add logs as this might add unnecessary fluff to the logs and the items still belong to the user.
            if(isset($data['stack_id'])) {
                foreach($data['stack_id'] as $key=>$stackId) {
                    $stack = UserItem::with('item')->find($stackId);
                    if(!$stack || $stack->user_id != $user->id) throw new \Exception("Invalid item selected.");
                    if(!isset($data['stack_quantity'][$key])) $data['stack_quantity'][$key] = $stack->count;
                    $stack->submission_count += $data['stack_quantity'][$key];
                    $stack->save();

                    addAsset($userAssets, $stack, $data['stack_quantity'][$key]);
                }
            }

            // Attach currencies.
            if(isset($data['currency_id'])) {
                foreach($data['currency_id'] as $holderKey=>$currencyIds) {
                    $holder = explode('-', $holderKey);
                    $holderType = $holder[0];
                    $holderId = $holder[1];

                    $holder = User::find($holderId);

                    $currencyManager = new CurrencyManager;
                    foreach($currencyIds as $key=>$currencyId) {
                        $currency = Currency::find($currencyId);
                        if(!$currency) throw new \Exception("Invalid currency selected.");
                        if(!$currencyManager->debitCurrency($holder, null, null, null, $currency, $data['currency_quantity'][$holderKey][$key])) throw new \Exception("Invalid currency/quantity selected.");

                        addAsset($userAssets, $currency, $data['currency_quantity'][$holderKey][$key]);

                    }
                }
            }
            if(!$isClaim) 
            {
                //level req
                if($prompt->level_req)
                {
                    if($user->level->current_level < $prompt->level_req) throw new \Exception('You are not high enough level to enter this prompt');
                }
                // focus character
                if(isset($data['focus_chara']))
                {
                    $focusCharacter = Character::where('slug', $data['focus_chara'])->first();
                    if(!$focusCharacter) throw new \Exception('Invalid character code entered for focus character');

                    $focusId = $focusCharacter->id;
                }
                else $focusId = NULL;
            }
            else $focusId = NULL;

            // Get a list of rewards, then create the submission itself
            $promptRewards = createAssetsArray();
            if(!$isClaim)
            {
                foreach($prompt->rewards as $reward)
                {
                    addAsset($promptRewards, $reward->reward, $reward->quantity);
                }
            }
            $promptRewards = mergeAssetsArrays($promptRewards, $this->processRewards($data, false));
            $submission = Submission::create([
                'user_id' => $user->id,
                'focus_chara_id' => $focusId,
                'url' => isset($data['url']) ? $data['url'] : null,
                'status' => 'Pending',
                'comments' => $data['comments'],
                'data' => json_encode([
                    'user' => Arr::only(getDataReadyAssets($userAssets), ['user_items','currencies']),
                    'rewards' => getDataReadyAssets($promptRewards)
                    ]) // list of rewards and addons
            ] + ($isClaim ? [] : ['prompt_id' => $prompt->id,]));

            // Retrieve all currency IDs for characters
            $currencyIds = [];
            if(isset($data['character_currency_id'])) {
                foreach($data['character_currency_id'] as $c)
                {
                    foreach($c as $currencyId) $currencyIds[] = $currencyId;
                }
            }
            array_unique($currencyIds);
            $currencies = Currency::whereIn('id', $currencyIds)->where('is_character_owned', 1)->get()->keyBy('id');

            // Attach characters
            foreach($characters as $c)
            {
                if($c->id == $focusId) throw new \Exception('Please only include the focus character in the focus character area.');
                // Users might not pass in clean arrays (may contain redundant data) so we need to clean that up
                $assets = $this->processRewards($data + ['character_id' => $c->id, 'currencies' => $currencies], true);

                // Now we have a clean set of assets (redundant data is gone, duplicate entries are merged)
                // so we can attach the character to the submission
                SubmissionCharacter::create([
                    'character_id' => $c->id,
                    'submission_id' => $submission->id,
                    'data' => json_encode(getDataReadyAssets($assets))
                ]);
            }

            return $this->commitReturn($submission);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
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
        else
        {
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
    }

    /**
     * Rejects a submission.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return mixed
     */
    public function rejectSubmission($data, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the submission exists
            // 2. check that the submission is pending
            if(!isset($data['submission'])) $submission = Submission::where('status', 'Pending')->where('id', $data['id'])->first();
            elseif($data['submission']->status == 'Pending') $submission = $data['submission'];
            else $submission = null;
            if(!$submission) throw new \Exception("Invalid submission.");

            // Return all added items
            $addonData = $submission->data['user'];
            if(isset($addonData['user_items'])) {
                foreach($addonData['user_items'] as $userItemId => $quantity) {
                    $userItemRow = UserItem::find($userItemId);
                    if(!$userItemRow) throw new \Exception("Cannot return an invalid item. (".$userItemId.")");
                    if($userItemRow->submission_count < $quantity) throw new \Exception("Cannot return more items than was held. (".$userItemId.")");
                    $userItemRow->submission_count -= $quantity;
                    $userItemRow->save();
                }
            }

            // And currencies
            $currencyManager = new CurrencyManager;
            if(isset($addonData['currencies']) && $addonData['currencies'])
            {
                foreach($addonData['currencies'] as $currencyId=>$quantity) {
                    $currency = Currency::find($currencyId);
                    if(!$currency) throw new \Exception("Cannot return an invalid currency. (".$currencyId.")");
                    if(!$currencyManager->creditCurrency(null, $submission->user, null, null, $currency, $quantity)) throw new \Exception("Could not return currency to user. (".$currencyId.")");
                }
            }

			if(isset($data['staff_comments']) && $data['staff_comments']) $data['parsed_staff_comments'] = parse($data['staff_comments']);
			else $data['parsed_staff_comments'] = null;

            // The only things we need to set are:
            // 1. staff comment
            // 2. staff ID
            // 3. status
            $submission->update([
                'staff_comments' => $data['staff_comments'],
				'parsed_staff_comments' => $data['parsed_staff_comments'],
                'staff_id' => $user->id,
                'status' => 'Rejected'
            ]);

            Notifications::create($submission->prompt_id ? 'SUBMISSION_REJECTED' : 'CLAIM_REJECTED', $submission->user, [
                'staff_url' => $user->url,
                'staff_name' => $user->name,
                'submission_id' => $submission->id,
            ]);

            return $this->commitReturn($submission);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Approves a submission.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return mixed
     */
    public function approveSubmission($data, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the submission exists
            // 2. check that the submission is pending
            $submission = Submission::where('status', 'Pending')->where('id', $data['id'])->first();
            if(!$submission) throw new \Exception("Invalid submission.");

            // Remove any added items, hold counts, and add logs
            $addonData = $submission->data['user'];
            $inventoryManager = new InventoryManager;
            if(isset($addonData['user_items'])) {
                $stacks = $addonData['user_items'];
                foreach($addonData['user_items'] as $userItemId => $quantity) {
                    $userItemRow = UserItem::find($userItemId);
                    if(!$userItemRow) throw new \Exception("Cannot return an invalid item. (".$userItemId.")");
                    if($userItemRow->submission_count < $quantity) throw new \Exception("Cannot return more items than was held. (".$userItemId.")");
                    $userItemRow->submission_count -= $quantity;
                    $userItemRow->save();
                }

                // Workaround for user not being unset after inventory shuffling, preventing proper staff ID assignment
                $staff = $user;

                foreach($stacks as $stackId=>$quantity) {
                    $stack = UserItem::find($stackId);
                    $user = User::find($submission->user_id);
                    if(!$inventoryManager->debitStack($user, $submission->prompt_id ? 'Prompt Approved' : 'Claim Approved', ['data' => 'Item used in submission (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)'], $stack, $quantity)) throw new \Exception("Failed to create log for item stack.");
                }

                // Set user back to the processing staff member, now that addons have been properly processed.
                $user = $staff;
            }

            // Log currency removal, etc.
            $currencyManager = new CurrencyManager;
            if(isset($addonData['currencies']) && $addonData['currencies'])
            {
                foreach($addonData['currencies'] as $currencyId=>$quantity) {
                    $currency = Currency::find($currencyId);
                    if(!$currencyManager->createLog($user->id, 'User', null, null,
                    $submission->prompt_id ? 'Prompt Approved' : 'Claim Approved', 'Used in ' . ($submission->prompt_id ? 'prompt' : 'claim') . ' (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)', $currencyId, $quantity))
                        throw new \Exception("Failed to create currency log.");
                }
            }

            // The character identification comes in both the slug field and as character IDs
            // that key the reward ID/quantity arrays.
            // We'll need to match characters to the rewards for them.
            // First, check if the characters are accessible to begin with.
            if(isset($data['slug'])) {
                $characters = Character::myo(0)->visible()->whereIn('slug', $data['slug'])->get();
                if(count($characters) != count($data['slug'])) throw new \Exception("One or more of the selected characters do not exist.");
            }
            else $characters = [];

            // Get the updated set of rewards
            $rewards = $this->processRewards($data, false, true);

            // Logging data
            $promptLogType = $submission->prompt_id ? 'Prompt Rewards' : 'Claim Rewards';
            $promptData = [
                'data' => 'Received rewards for '.($submission->prompt_id ? 'submission' : 'claim').' (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)'
            ];

            // Distribute user rewards
            if(!$rewards = fillUserAssets($rewards, $user, $submission->user, $promptLogType, $promptData)) throw new \Exception("Failed to distribute rewards to user.");

            // Retrieve all currency IDs for characters
            $currencyIds = [];
            if(isset($data['character_currency_id'])) {
                foreach($data['character_currency_id'] as $c)
                    foreach($c as $currencyId) $currencyIds[] = $currencyId;
            }
            array_unique($currencyIds);
            $currencies = Currency::whereIn('id', $currencyIds)->where('is_character_owned', 1)->get()->keyBy('id');

            // We're going to remove all characters from the submission and reattach them with the updated data
            $submission->characters()->delete();

            // Distribute character rewards
            foreach($characters as $c)
            {
                // Users might not pass in clean arrays (may contain redundant data) so we need to clean that up
                $assets = $this->processRewards($data + ['character_id' => $c->id, 'currencies' => $currencies], true);

                if(!fillCharacterAssets($assets, $user, $c, $promptLogType, $promptData)) throw new \Exception("Failed to distribute rewards to character.");

                SubmissionCharacter::create([
                    'character_id' => $c->id,
                    'submission_id' => $submission->id,
                    'data' => json_encode(getDataReadyAssets($assets))
                ]);
            }

            // stats & exp ---- currently prompt only
            if($submission->prompt_id && $submission->prompt->expreward)
            {
                $levelLog = new ExperienceManager;
                $statLog = new StatManager;
                $levelData = 'Received rewards for '.($submission->prompt_id ? 'submission' : 'claim').' (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)';
                
                // to be encoded
                $user_exp = null;
                $user_points = null;
                $character_exp = null;
                $character_points = null;
                // user
                $level = $submission->user->level;
                $levelUser = $submission->user;
                if(!$level) throw new \Exception('This user does not have a level log.');

                // exp
                if($submission->prompt->expreward->user_exp || $data['bonus_user_exp'])
                {
                    $quantity = $submission->prompt->expreward->user_exp;
                        if($data['bonus_user_exp'])
                        {
                            $quantity += $data['bonus_user_exp'];
                        }
                        $user_exp += $quantity;
                    if(!$levelLog->creditExp(null, $levelUser, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant user exp');
                }
                //points
                if($submission->prompt->expreward->user_points || $data['bonus_user_points'])
                {
                    $quantity = $submission->prompt->expreward->user_points;
                        if($data['bonus_user_points'])
                        {
                            $quantity += $data['bonus_user_points'];
                        }
                        $user_points += $quantity;
                    if(!$statLog->creditStat(null, $levelUser, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant user points');
                }

                // character
                if($submission->focus_chara_id)
                {
                    $level = $submission->focus->level;
                    $levelCharacter = $submission->focus;
                    if(!$level) throw new \Exception('This character does not have a level log.');
                    // exp
                    if($submission->prompt->expreward->chara_exp || $data['bonus_exp'])
                    {
                        $quantity = $submission->prompt->expreward->chara_exp;
                        if($data['bonus_exp'])
                        {
                            $quantity += $data['bonus_exp'];
                        }
                        $character_exp += $quantity;
                        if(!$levelLog->creditExp(null, $levelCharacter, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant character exp');
                    }
                    // points
                    if($submission->prompt->expreward->chara_points || $data['bonus_points'])
                    {
                        $quantity = $submission->prompt->expreward->chara_points;
                        if($data['bonus_points'])
                        {
                            $quantity += $data['bonus_points'];
                        }
                        $character_points += $quantity;
                        if(!$statLog->creditStat(null, $levelCharacter, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant character points');
                    }
                }

                $json[] = [
                    'User_Bonus' => [
                        'exp' => $user_exp,
                        'points' => $user_points
                    ],
                    'Character_Bonus' => [
                        'exp' => $character_exp,
                        'points' => $character_points
                    ],
                ];

                $bonus = json_encode($json);
            }
            else $bonus = NULL;

            // Increment user submission count if it's a prompt
            if($submission->prompt_id) {
                $user->settings->submission_count++;
                $user->settings->save();
            }

			if(isset($data['staff_comments']) && $data['staff_comments']) $data['parsed_staff_comments'] = parse($data['staff_comments']);
			else $data['parsed_staff_comments'] = null;

            // Finally, set:
			// 1. staff comments
            // 2. staff ID
            // 3. status
            // 4. final rewards
            $submission->update([
			    'staff_comments' => $data['staff_comments'],
				'parsed_staff_comments' => $data['parsed_staff_comments'],
                'staff_id' => $user->id,
                'status' => 'Approved',
                'data' => json_encode([
                    'user' => $addonData,
                    'rewards' => getDataReadyAssets($rewards)
                    ]), // list of rewards
                'bonus' => $bonus,
            ]);

            Notifications::create($submission->prompt_id ? 'SUBMISSION_APPROVED' : 'CLAIM_APPROVED', $submission->user, [
                'staff_url' => $user->url,
                'staff_name' => $user->name,
                'submission_id' => $submission->id,
            ]);

            return $this->commitReturn($submission);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
