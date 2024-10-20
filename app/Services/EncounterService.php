<?php namespace App\Services;

use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Encounter\AreaEncounters;
use App\Models\Encounter\AreaLimit;
use App\Models\Encounter\Encounter;
use App\Models\Encounter\EncounterArea;
use App\Models\Encounter\EncounterPrompt;
use App\Models\Encounter\PromptLimit;
use App\Models\User\User;
use App\Services\CurrencyManager;
use App\Services\Service;
use Config;
use DB;
use Illuminate\Support\Arr;

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
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $thumb = null;
            if (isset($data['thumb']) && $data['thumb']) {
                $data['has_thumbnail'] = 1;
                $thumb = $data['thumb'];
                unset($data['thumb']);
            } else {
                $data['has_thumbnail'] = 0;
            }

            if (!isset($data['encounter_id'])) {
                throw new \Exception('Areas must have at least one encounter.');
            }

            if (isset($data['encounter_id'])) {
                foreach ($data['encounter_id'] as $key => $encounter) {
                    if (!$encounter) {
                        throw new \Exception('Please select an encounter.');
                    }
                }
            }

            $area = EncounterArea::create($data);

            $this->populateTable($area, Arr::only($data, ['encounter_id', 'weight']));

            if ($image) {
                $this->handleImage($image, $area->imagePath, $area->imageFileName);
            }
            if ($thumb) {
                $this->handleImage($thumb, $area->thumbImagePath, $area->thumbImageFileName);
            }

            return $this->commitReturn($area);
        } catch (\Exception $e) {
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
            if (
                EncounterArea::where('name', $data['name'])
                ->where('id', '!=', $area->id)
                ->exists()
            ) {
                throw new \Exception('The name has already been taken.');
            }

            if (!isset($data['encounter_id'])) {
                throw new \Exception('Areas must have at least one encounter.');
            }

            if (isset($data['encounter_id'])) {
                foreach ($data['encounter_id'] as $key => $encounter) {
                    if (!$encounter) {
                        throw new \Exception('Please select an encounter.');
                    }
                }
            }

            $data = $this->populateAreaData($data, $area);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $thumb = null;
            if (isset($data['thumb']) && $data['thumb']) {
                $data['has_thumbnail'] = 1;
                $thumb = $data['thumb'];
                unset($data['thumb']);
            }

            $area->update($data);
            $this->populateTable($area, Arr::only($data, ['encounter_id', 'weight']));

            if ($area) {
                $this->handleImage($image, $area->imagePath, $area->imageFileName);
            }
            if ($area) {
                $this->handleImage($thumb, $area->thumbImagePath, $area->thumbImageFileName);
            }

            return $this->commitReturn($area);
        } catch (\Exception $e) {
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
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } elseif (!isset($data['description']) && !$data['description']) {
            $data['parsed_description'] = null;
        }

        isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : ($data['is_active'] = 0);

        if (isset($data['remove_image'])) {
            if ($area && $area->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($area->imagePath, $area->imageFileName);
            }
            unset($data['remove_image']);
        }

        if (isset($data['remove_thumb'])) {
            if ($area && $area->has_thumbnail && $data['remove_thumb']) {
                $data['has_thumbnail'] = 0;
                $this->deleteImage($area->thumbImagePath, $area->thumbImageFileName);
            }
            unset($data['remove_thumb']);
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
            if ($area->has_image) {
                $this->deleteImage($area->imagePath, $area->imageFileName);
            }
            $area->encounters()->delete();
            $area->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
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

        foreach ($data['encounter_id'] as $key => $type) {
            AreaEncounters::create([
                'encounter_area_id' => $area->id,
                'encounter_id' => isset($type) ? $type : 1,
                'weight' => $data['weight'][$key],
            ]);
        }
    }

    /**
     * Restrict an area behind items
     *
     * @param  \App\Models\Encounter\Encounter  $season
     * @param  array                       $data
     */
    public function restrictArea($data, $id)
    {
        DB::beginTransaction();

        try {
            $area = EncounterArea::find($id);

            $area->limits()->delete();

            if (isset($data['item_type'])) {
                foreach ($data['item_type'] as $key => $type) {
                    AreaLimit::create([
                        'encounter_area_id' => $area->id,
                        'item_type' => $type,
                        'item_id' => $data['item_id'][$key],
                    ]);
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
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
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $encounter = Encounter::create($data);
            $encounter->update([
                'extras' => json_encode([
                    'position_right' => isset($data['position_right']) && $data['position_right'] ? $data['position_right'] : null,
                    'position_bottom' => isset($data['position_bottom']) && $data['position_bottom'] ? $data['position_bottom'] : null,
                ]),
            ]);

            if ($image) {
                $this->handleImage($image, $encounter->imagePath, $encounter->imageFileName);
            }

            return $this->commitReturn($encounter);
        } catch (\Exception $e) {
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
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $encounter->update($data);
            $encounter->update([
                'extras' => json_encode([
                    'position_right' => isset($data['position_right']) && $data['position_right'] ? $data['position_right'] : null,
                    'position_bottom' => isset($data['position_bottom']) && $data['position_bottom'] ? $data['position_bottom'] : null,
                ]),
            ]);

            if ($encounter) {
                $this->handleImage($image, $encounter->imagePath, $encounter->imageFileName);
            }

            return $this->commitReturn($encounter);
        } catch (\Exception $e) {
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
        if (isset($data['initial_prompt']) && $data['initial_prompt']) {
            $data['initial_prompt'] = parse($data['initial_prompt']);
        } elseif (!isset($data['initial_prompt']) && !$data['initial_prompt']) {
            $data['initial_prompt'] = null;
        }

        isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : ($data['is_active'] = 0);

        if (isset($data['remove_image'])) {
            if ($encounter && $encounter->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($encounter->imagePath, $encounter->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
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
            if (AreaEncounters::where('encounter_id', $encounter->id)->exists()) {
                throw new \Exception('An area has this encounter as an option. Please remove it from the list first.');
            }

            if ($encounter->has_image) {
                $this->deleteImage($encounter->imagePath, $encounter->imageFileName);
            }
            $encounter->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

    ENCOUNTER EXPLORATION

     **********************************************************************************************/
    /**
     * Creates a new prompt for a encounter
     */
    public function createPrompt($encounter, $data)
    {
        DB::beginTransaction();

        try {
            if ($data['result_type'] == null) {
                throw new \Exception('Encounter prompts must have a result type.');
            }

            if (isset($data['rewardable_type'])) {
                foreach ($data['rewardable_type'] as $key => $type) {
                    if (!$type) {
                        throw new \Exception('Please select a reward type.');
                    }
                    if (!$data['rewardable_id'][$key]) {
                        throw new \Exception('Please select a reward');
                    }
                    if (!$data['quantity'][$key] || $data['quantity'][$key] < 1) {
                        throw new \Exception('Quantity is required and must be an integer greater than 0.');
                    }
                }
            }

            $prompt = EncounterPrompt::create([
                'encounter_id' => $encounter->id,
                'name' => $data['name'],
                'result' => parse($data['result']),
            ]);

            $prompt->update([
                'extras' => json_encode([
                    'math_type' => isset($data['math_type']) && $data['math_type'] ? $data['math_type'] : null,
                    'energy_value' => isset($data['energy_value']) && $data['energy_value'] ? $data['energy_value'] : null,
                    'result_type' => isset($data['result_type']) && $data['result_type'] ? $data['result_type'] : null,
                ]),
            ]);

            $prompt->output = $this->populateRewards($data);
            $this->populatePromptLimits($data, $prompt->id);
            $prompt->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Edits the prompts on a encounter
     */
    public function editPrompt($prompt, $data)
    {
        DB::beginTransaction();

        try {
            if ($data['result_type'] == null) {
                throw new \Exception('Encounter prompts must have a result type.');
            }

            $prompt->update([
                'name' => $data['name'],
                'result' => parse($data['result']),
            ]);

            if (isset($data['rewardable_type'])) {
                foreach ($data['rewardable_type'] as $key => $type) {
                    if (!$type) {
                        throw new \Exception('Please select a reward type.');
                    }
                    if (!$data['rewardable_id'][$key]) {
                        throw new \Exception('Please select a reward');
                    }
                    if (!$data['quantity'][$key] || $data['quantity'][$key] < 1) {
                        throw new \Exception('Quantity is required and must be an integer greater than 0.');
                    }
                }
            }

            $prompt->update([
                'extras' => json_encode([
                    'math_type' => isset($data['math_type']) && $data['math_type'] ? $data['math_type'] : null,
                    'energy_value' => isset($data['energy_value']) && $data['energy_value'] ? $data['energy_value'] : null,
                    'result_type' => isset($data['result_type']) && $data['result_type'] ? $data['result_type'] : null,
                ]),
            ]);

            $prompt->output = $this->populateRewards($data);
            $this->populatePromptLimits($data, $prompt->id);
            $prompt->save();

            if (isset($data['delete']) && $data['delete']) {
                $prompt->delete();
                flash('Option deleted successfully.')->success();
            } else {
                flash('Option updated successfully.')->success();
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Creates the assets json from rewards
     *
     * @param  \App\Models\Recipe\Recipe   $recipe
     * @param  array                       $data
     */
    private function populateRewards($data)
    {
        if (isset($data['rewardable_type'])) {
            // The data will be stored as an asset table, json_encode()d.
            // First build the asset table, then prepare it for storage.
            $assets = createAssetsArray();
            foreach ($data['rewardable_type'] as $key => $r) {
                switch ($r) {
                    case 'Item':
                        $type = 'App\Models\Item\Item';
                        break;
                    case 'Currency':
                        $type = 'App\Models\Currency\Currency';
                        break;
                    case 'LootTable':
                        $type = 'App\Models\Loot\LootTable';
                        break;
                    case 'Raffle':
                        $type = 'App\Models\Raffle\Raffle';
                        break;
                }
                $asset = $type::find($data['rewardable_id'][$key]);
                addAsset($assets, $asset, $data['quantity'][$key]);
            }

            return getDataReadyAssets($assets);
        }
        return null;
    }

    /**
     * Restrict an area behind items
     *
     * @param  \App\Models\Encounter\Encounter  $season
     * @param  array                       $data
     */
    public function populatePromptLimits($data, $id)
    {
        DB::beginTransaction();

        try {
            if (isset($data['item_type'])) {
                foreach ($data['item_type'] as $key => $type) {
                    if (!$type) {
                        throw new \Exception('Please select a limit type.');
                    }
                    if (!$data['item_id'][$key]) {
                        throw new \Exception('Please select a limit');
                    }
                }
            }

            $prompt = EncounterPrompt::find($id);

            $prompt->limits()->delete();

            if (isset($data['item_type'])) {
                foreach ($data['item_type'] as $key => $type) {
                    PromptLimit::create([
                        'encounter_prompt_id' => $prompt->id,
                        'item_type' => $type,
                        'item_id' => $data['item_id'][$key],
                    ]);
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
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
            if (!$data['action']) {
                abort(404);
            }
            $area = EncounterArea::active()->find($data['area_id']);
            if (!$area) {
                abort(404);
            }
            $action = EncounterPrompt::find($data['action']);
            if (!$action) {
                abort(404);
            }

            $encounter = $action->encounter;

            if ($action->extras['result_type'] == 'success') {
                flash('<div class="text-center"><p>' . $action->result . '</p></div>')->success();
            } elseif ($action->extras['result_type'] == 'neutral') {
                flash('<div class="text-center"><p>' . $action->result . '</p></div>');
            } else {
                flash('<div class="text-center"><p>' . $action->result . '</p></div>')->error();
            }

            //if there is a reward, credit it
            if ($action->output != null) {
                // Credit rewards
                $logType = 'Encounter Reward';
                $rewardData = [
                    'data' => 'Received rewards from ' . $encounter->name . ' encounter',
                ];

                if (!($rewards = fillUserAssets($action->rewardItems, null, $user, $logType, $rewardData))) {
                    throw new \Exception('Failed to distribute rewards to user.');
                }
                flash($this->getRewardsString($rewards));
            }

            $use_energy = Config::get('lorekeeper.encounters.use_energy');
            $use_characters = Config::get('lorekeeper.encounters.use_characters');

            //if it alters the energy, then alter it
            if ($action->extras != null && $action->extras['math_type'] != null && $action->extras['energy_value'] != null) {
                if ($use_characters) {
                    $this->grantRemoveEnergy($action, $user, true, $area, $user->settings->encounterCharacter);
                } else {
                    $this->grantRemoveEnergy($action, $user, false, $area);
                }

            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * flash what the user got from the encounter
     *
     * @param  array                  $rewards
     * @return string
     */
    private function getRewardsString($rewards)
    {
        return 'You have received: ' . createRewardsString($rewards);
    }

    /**
     * Select character
     */
    public function selectCharacter($user, $id)
    {
        DB::beginTransaction();

        try {
            if (!$id) {
                throw new \Exception('Please select a character.');
            }
            $character = Character::find($id);
            if (!$character) {
                throw new \Exception('Invalid character.');
            }
            if ($character->user_id != $user->id) {
                throw new \Exception('You do not own this character.');
            }

            $user->settings->encounter_character_id = $id;
            $user->settings->save();

            return $this->commitReturn($user);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * grant energy to user or character
     *
     * @param array $data
     * @param User  $staff
     *
     * @return bool
     */
    public function grantEncounterEnergy($data, $staff)
    {
        DB::beginTransaction();

        try {
            $use_energy = Config::get('lorekeeper.encounters.use_energy');
            //abort if currency is selected
            //no point in using this page if so lmao.
            if (!$use_energy) {
                abort(404);
            }
            $use_characters = Config::get('lorekeeper.encounters.use_characters');

            $users = null;
            $characters = null;

            // ignore the opposite data depending on setting
            if ($use_characters) {
                if (isset($data['character_names'])) {
                    $characters = Character::find($data['character_names']);
                    if (count($characters) != count($data['character_names'])) {
                        throw new \Exception('An invalid character was selected.');
                    }
                }
            } else {
                if (isset($data['names'])) {
                    $users = User::find($data['names']);
                    if (count($users) != count($data['names'])) {
                        throw new \Exception('An invalid user was selected.');
                    }
                }
            }

            if ($data['quantity'] == 0) {
                throw new \Exception("Please enter a non-zero quantity.");
            }

            foreach ([$users, $characters] as $targets) {
                if (!$targets) {
                    continue;
                }

                $quantity = $data['quantity'];

                foreach ($targets as $target) {
                    //path to debit or grant
                    if ($use_characters) {
                        $path = $target;
                    } else {
                        $path = $target->settings;
                    }

                    if ($quantity < 0) {
                        $path->encounter_energy -= -$quantity;
                        $path->save();
                    } else {
                        $path->encounter_energy += $quantity;
                        $path->save();
                    }

                }

            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    public function grantRemoveEnergy($action, $user, $use_characters, $area, $character = null)
    {
        //let's try and compact some of these checks

        $use_energy = Config::get('lorekeeper.encounters.use_energy');
        $use_characters = Config::get('lorekeeper.encounters.use_characters');

        //get paths to grant or debit
        if ($use_characters) {
            $recipient = $character;
            $currencyrecipient = $character;
        } else {
            $recipient = $user->settings;
            $currencyrecipient = $user;
        }

        //if set to use energy
        if ($use_energy) {

            //math.
            $operators = [
                'add' => '+',
                'subtract' => '-',
            ];

            $quantity = eval('return ' . $recipient->encounter_energy . $operators[$action->extras['math_type']] . $action->extras['energy_value'] . ';');

            $recipient->encounter_energy = $quantity;
            $recipient->save();

            //if would become negative set to 0
            if ($recipient->encounter_energy < 0) {
                $recipient->encounter_energy = 0;
                $recipient->save();
            }
        } else {
            //use currency
            if ($action->extras['math_type'] == 'subtract') {
                if (!(new CurrencyManager())->debitCurrency($currencyrecipient, null, 'Encounter Removal', 'Lost energy in ' . $area->name . '...', Currency::find(Config::get('lorekeeper.encounters.energy_replacement_id')), $action->extras['energy_value'])) {
                    flash('Could not debit currency.')->error();
                    return redirect()->back();
                }
            } else {
                if (!(new CurrencyManager())->creditCurrency(null, $currencyrecipient, 'Encounter Grant', 'Gained energy in ' . $area->name . '!', Currency::find(Config::get('lorekeeper.encounters.energy_replacement_id')), $action->extras['energy_value'])) {
                    flash('Could not grant currency.')->error();
                    return redirect()->back();
                }
            }

        }
        if ($action->extras['math_type'] == 'subtract') {
            flash(($currencyrecipient->logType == 'User' ? 'You' : $character->fullName) . ' lost ' . $action->extras['energy_value'] . ' energy...')->error();
        } elseif ($action->extras['math_type'] == 'add') {
            flash(($currencyrecipient->logType == 'User' ? 'You' : $character->fullName) . ' regained ' . $action->extras['energy_value'] . ' energy!')->success();
        }

    }
}
