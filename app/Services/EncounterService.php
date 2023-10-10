<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use Illuminate\Support\Arr;
use App\Models\Encounter\Encounter;
use App\Models\Encounter\EncounterReward;
use App\Models\Submission\Submission;

class EncounterService extends Service
{
    /**
     * Creates a new encounter.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Encounter\Encounter
     */
    public function createEncounter($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $encounter = Encounter::create(Arr::only($data, ['name', 'initial_prompt', 'proceed_prompt', 'dont_proceed_prompt', 'is_active']));

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $encounter);

            return $this->commitReturn($encounter);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a encounter.
     *
     * @param  \App\Models\Encounter\Encounter  $Encounter
     * @param  array                      $data
     * @param  \App\Models\User\User      $user
     * @return bool|\App\Models\Encounter\Encounter
     */
    public function updateEncounter($encounter, $data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data, $encounter);

            $encounter->update(Arr::only($data, ['name', 'initial_prompt', 'proceed_prompt', 'dont_proceed_prompt', 'is_active']));

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $encounter);

            return $this->commitReturn($encounter);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a encounter.
     *
     * @param  array                      $data
     * @param  \App\Models\Encounter\Encounter  $encounter
     * @return array
     */
    private function populateData($data, $encounter = null)
    {
        if(isset($data['initial_prompt']) && $data['initial_prompt']) $data['parsed_description'] = parse($data['initial_prompt']);
        if(isset($data['dont_proceed_prompt']) && $data['dont_proceed_prompt']) $data['parsed_description'] = parse($data['dont_proceed_prompt']);
        if(isset($data['dont_proceed_prompt']) && $data['dont_proceed_prompt']) $data['parsed_description'] = parse($data['proceed_prompt']);
        return $data;
    }

    /**
     * Processes user input for creating/updating encounter rewards.
     *
     * @param  array                      $data
     * @param  \App\Models\Encounter\Encounter  $encounter
     */
    private function populateRewards($data, $encounter)
    {
        // Clear the old rewards...
        $encounter->rewards()->delete();

        if(isset($data['rewardable_type'])) {
            foreach($data['rewardable_type'] as $key => $type)
            {
                EncounterReward::create([
                    'encounter_id'       => $encounter->id,
                    'rewardable_type' => $type,
                    'rewardable_id'   => $data['rewardable_id'][$key],
                    'quantity'        => $data['quantity'][$key],
                ]);
            }
        }
    }
}