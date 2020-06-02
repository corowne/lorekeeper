<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;
use Config;
use Image;
use Notifications;
use Settings;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Loot\LootTable;
use App\Models\Prompt\Prompt;

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
                'url' => $data['url'],
                'status' => 'Pending',
                'comments' => $data['comments'],
                'data' => json_encode(getDataReadyAssets($promptRewards)) // list of rewards
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
                'data' => json_encode(getDataReadyAssets($rewards))
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