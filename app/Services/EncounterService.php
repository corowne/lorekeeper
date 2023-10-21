<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use Illuminate\Support\Arr;
use App\Models\Encounter\Encounter;
use App\Models\Encounter\EncounterArea;
use App\Models\Encounter\EncounterReward;
use App\Models\Encounter\AreaEncounters;
use App\Models\Encounter\EncounterPrompt;

class EncounterService extends Service
{

    /**********************************************************************************************

        ENCOUNTER AREAS

    **********************************************************************************************/

    /**
     * Create a area.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return \App\Models\Prompt\EncounterArea|bool
     */
    public function createEncounterArea($data, $user)
    {
        DB::beginTransaction();

        try {

            $data = $this->populateAreaData($data);


            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $area = EncounterArea::create($data);

            $this->populateTable($area, Arr::only($data, ['encounter_id', 'weight']));

            if ($image) $this->handleImage($image, $area->imagePath, $area->imageFileName);

            return $this->commitReturn($area);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a area.
     *
     * @param  \App\Models\Prompt\EncounterArea  $area
     * @param  array                              $data
     * @param  \App\Models\User\User              $user
     * @return \App\Models\Prompt\EncounterArea|bool
     */
    public function updateEncounterArea($area, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(EncounterArea::where('name', $data['name'])->where('id', '!=', $area->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateAreaData($data, $area);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $area->update($data);
            $this->populateTable($area, Arr::only($data, ['encounter_id', 'weight']));

            if ($area) $this->handleImage($image, $area->imagePath, $area->imageFileName);

            return $this->commitReturn($area);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handle area data.
     *
     * @param  array                                   $data
     * @param  \App\Models\Prompt\EncounterArea|null  $area
     * @return array
     */
    private function populateAreaData($data, $area = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        elseif(!isset($data['description']) && !$data['description']) $data['parsed_description'] = null;

        isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : $data['is_active'] = 0;

        if(isset($data['remove_image']))
        {
            if($area && $area->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($area->imagePath, $area->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Delete a area.
     *
     * @param  \App\Models\Prompt\EncounterArea  $area
     * @return bool
     */
    public function deleteEncounterArea($area)
    {
        DB::beginTransaction();

        try {

            if($area->has_image) $this->deleteImage($area->imagePath, $area->imageFileName);
            $area->encounters()->delete();
            $area->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handles the creation of encounter tables for an area.
     *
     * @param  \App\Models\Encounter\Encounter  $season
     * @param  array                       $data
     */
    private function populateTable($area, $data)
    {
        // Clear the old encounters...
        $area->encounters()->delete();

        foreach ($data['encounter_id'] as $key => $type)
        {
            AreaEncounters::create([
                'encounter_area_id'   => $area->id,
                'encounter_id'   => isset($type) ? $type : 1,
                'weight'          => $data['weight'][$key],
            ]);
        }
    }


    /**********************************************************************************************

        ENCOUNTERS

    **********************************************************************************************/

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

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $encounter = Encounter::create($data);

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $encounter);

            $this->populatePrompts(Arr::only($data, ['option_name', 'option_description', 'option_reward']), $encounter);

            if ($image) $this->handleImage($image, $encounter->imagePath, $encounter->imageFileName);

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

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $encounter->update($data);

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $encounter);
            $this->populatePrompts(Arr::only($data, ['option_name', 'option_description', 'option_reward']), $encounter);

            if ($encounter) $this->handleImage($image, $encounter->imagePath, $encounter->imageFileName);

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

        if(isset($data['initial_prompt']) && $data['initial_prompt']) $data['initial_prompt'] = parse($data['initial_prompt']);
        elseif(!isset($data['initial_prompt']) && !$data['initial_prompt']) $data['initial_prompt'] = null;

        isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : $data['is_active'] = 0;

        if(isset($data['remove_image']))
        {
            if($encounter && $encounter->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($encounter->imagePath, $encounter->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

        /**
     * Processes user input for creating/updating encounter rewards.
     *
     * @param  array                      $data
     * @param  \App\Models\Encounter\Encounter  $encounter
     */
    private function populatePrompts($data, $encounter)
    {
        // Clear the old rewards...
        $encounter->prompts()->delete();

        if(isset($data['option_name'])) {
            foreach($data['option_name'] as $key => $type)
            {
                EncounterPrompt::create([
                    'encounter_id'       => $encounter->id,
                    'name' => $type,
                    'result'   => $data['option_description'][$key],
                    'give_reward' => isset($data['option_reward'][$key]),
                ]);
            }
        }
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

        /**
     * Deletes a encounter.
     *
     * @param  \App\Models\Prompt\Prompt  $encounter
     * @return bool
     */
    public function deleteEncounter($encounter)
    {
        DB::beginTransaction();

        try {
            // Check first if the encounter is currently in use
            if(AreaEncounters::where('encounter_id', $encounter->id)->exists()) throw new \Exception("An area has this encounter as an option. Please remove it from the list first.");

            $encounter->rewards()->delete();
            if($encounter->has_image) $this->deleteImage($encounter->imagePath, $encounter->imageFileName);
            $encounter->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

     /**********************************************************************************************

        ENCOUNTER EXPLORATION

    **********************************************************************************************/


    /**
     * Explore area
     *
     * @param  \App\Models\Prompt\Prompt  $encounter
     * @return bool
     */
    public function takeAction($id, $data, $user)
    {
        DB::beginTransaction();

        try {
        $area = EncounterArea::active()->find($data['area_id']);
        if (!$area) {
            abort(404);
        }
        $action = EncounterPrompt::find($data['action']);
        if (!$action) {
            abort(404);
        }
        $encounter = $action->encounter;
        
        if($action->give_reward){
            //action succeeds. give user rewards
           
           //i'd put it here if it worked lol
            flash('<div class="text-center"><strong>SUCCESS:</strong><p>'.$action->result.'</p></div>')->success();

        }else{
            flash('<div class="text-center"><strong>FAILURE:</strong><p>'.$action->result.'</p></div>')->error();
        }
        

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}