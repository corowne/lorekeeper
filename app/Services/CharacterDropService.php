<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Item\Item;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\Character\CharacterDropData;

class CharacterDropService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Character Drop Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character drops.
    |
    */

    /**
     * Creates character drop data.
     *
     * @param  array  $data
     * @return bool|\App\Models\Character\CharacterDropData
     */
    public function createCharacterDrop($data)
    {
        DB::beginTransaction();

        try {
            // Check to see if species exists
            $species = Species::find($data['species_id']);
            if(!$species) throw new \Exception('The selected species is invalid.');

            // Collect parameter data and encode it
            $paramData = [];
            foreach($data['label'] as $key => $param) $paramData[$param] = $data['weight'][$key];
            $data['parameters'] = json_encode($paramData);

            $data['data']['frequency'] = ['frequency' => $data['drop_frequency'], 'interval' => $data['drop_interval']];
            $data['data']['is_active'] = isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : 0;
            $data['data']['drop_name'] = isset($data['drop_name']) ? $data['drop_name'] : null;
            $data['data'] = json_encode($data['data']);

            $drop = CharacterDropData::create(array_only($data, ['species_id', 'parameters', 'data']));

            return $this->commitReturn($drop);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates character drop data.
     *
     * @param  \App\Models\Character\CharacterDropData  $drop
     * @param  array                                    $data
     * @return bool|\App\Models\Character\CharacterDropData
     */
    public function updateCharacterDrop($drop, $data)
    {
        DB::beginTransaction();

        try {
            // Check to see if species exists and if drop data already exists for it.
            $species = Species::find($data['species_id']);
            if(!$species) throw new \Exception('The selected species is invalid.');
            if(CharacterDropData::where('species_id', $data['species_id'])->where('id', '!=', $drop->id)->exists()) throw new \Exception('This species already has drop data. Consider editing the existing data instead.');

            // Collect parameter data and encode it
            $paramData = [];
            foreach($data['label'] as $key => $param) $paramData[$param] = $data['weight'][$key];
            $data['parameters'] = json_encode($paramData);

            // Validate items and process the data if appropriate
            if(isset($data['item_id']) && $data['item_id']) {
                foreach($data['item_id'] as $key=>$itemData) foreach($itemData as $param=>$itemId) {
                    if(isset($itemId) && $itemId) {
                        $item = Item::find($itemId);
                        if(!$item) throw new \Exception('One or more of the items selected are invalid.');

                        // Check if the quantities are valid and if only one is provided/they should be the same number
                        $minQuantity = $data['min_quantity'][$key][$param];
                        $maxQuantity = $data['max_quantity'][$key][$param];
                        if(!$minQuantity && !$maxQuantity) throw new \Exception('One or more of the items does not have either a minimum or maximum quantity.');
                        if(!$minQuantity || !$maxQuantity) {
                            if($minQuantity && !$maxQuantity) $maxQuantity = $minQuantity;
                            if(!$minQuantity && $maxQuantity) $minQuantity = $maxQuantity;
                        }

                        $data['data']['items'][$key][$param] = ['item_id' => $itemId, 'min' => $minQuantity, 'max' => $maxQuantity];
                    }
                }
            }

            $data['data']['frequency'] = ['frequency' => $data['drop_frequency'], 'interval' => $data['drop_interval']];
            $data['data']['is_active'] = isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : 0;
            $data['data']['drop_name'] = isset($data['drop_name']) ? $data['drop_name'] : null;
            $data['data'] = json_encode($data['data']);

            $drop->update(array_only($data, ['species_id', 'parameters', 'data']));

            return $this->commitReturn($drop);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes character drop data.
     *
     * @param  \App\Models\Character\CharacterDropData  $drop
     * @return bool
     */
    public function deleteCharacterDrop($drop)
    {
        DB::beginTransaction();

        try {
            // Check first if the table is currently in use
            // - Prompts
            // - Box rewards (unfortunately this can't be checked easily)
            if(CharacterDrop::where('drop_id', $drop->id)->exists()) throw new \Exception('A character has drops using this data. Consider disabling drops instead.');

            $drop->characterDrops()->delete();
            $drop->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
