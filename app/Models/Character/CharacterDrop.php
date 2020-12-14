<?php

namespace App\Models\Character;

use Config;
use DB;
use Carbon\Carbon;
use Notifications;
use App\Models\Model;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Item\Item;
use App\Models\Item\ItemLog;

class CharacterDrop extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'drop_id', 'character_id', 'parameters', 'drops_available', 'next_day'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_drops';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['next_day'];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the associated character.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }

    /**
     * Get the category the character belongs to.
     */
    public function dropData()
    {
        return $this->belongsTo('App\Models\Character\CharacterDropData', 'drop_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include drops that require updating.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresUpdate($query)
    {
        return $query->whereNotIn('character_id', Character::where('is_myo_slot', 1)->pluck('id')->toArray())->whereIn('drop_id', CharacterDropData::where('data->is_active', 1)->pluck('id')->toArray())->where('next_day', '<', Carbon::now());
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the item(s) a given character should be dropping.
     *
     */
    public function getSpeciesItemAttribute()
    {
        // Collect data from the drop data about what item this species drops.
        $itemsData = $this->dropData->data['items'];
        $speciesItem = isset($itemsData['species']) && isset($itemsData['species'][$this->parameters]) ? Item::find($itemsData['species'][$this->parameters]['item_id']) : null;
        if($speciesItem) return $speciesItem;
        else return null;
    }

    /**
     * Get quantity or quantity range for species drop.
     *
     */
    public function getSpeciesQuantityAttribute()
    {
        if($this->speciesItem) {
            $itemsData = $this->dropData->data['items'];
            $min = $itemsData['species'][$this->parameters]['min'];
            $max = $itemsData['species'][$this->parameters]['max'];
            if($min == $max) return $min;
            else return $min.'-'.$max;
        }
    }

    /**
     * Get the item(s) a given character should be dropping.
     *
     */
    public function getSubtypeItemAttribute()
    {
        // Collect data from the drop data about what item this species drops.
        $itemsData = $this->dropData->data['items'];
        $subtypeItem = isset($this->character->image->subtype_id) && isset($itemsData[$this->character->image->subtype_id][$this->parameters]) ? Item::find($itemsData[$this->character->image->subtype_id][$this->parameters]['item_id']) : null;
        if($subtypeItem) return $subtypeItem;
        else return null;
    }

    /**
     * Get quantity or quantity range for species drop.
     *
     */
    public function getSubtypeQuantityAttribute()
    {
        if($this->subtypeItem) {
            $itemsData = $this->dropData->data['items'];
            $min = $itemsData[$this->character->image->subtype_id][$this->parameters]['min'];
            $max = $itemsData[$this->character->image->subtype_id][$this->parameters]['max'];
            if($min == $max) return $min;
            else return $min.'-'.$max;
        }
    }

    /**
     * Get the item(s) a given character should be dropping.
     *
     */
    public function getItemsAttribute()
    {
        // Collect resulting items
        $items = collect([]);
        if($this->speciesItem) $items = $items->concat([$this->speciesItem]);
        if($this->subtypeItem) $items = $items->concat([$this->subtypeItem]);

        return $items;
    }

    /**
     * Get the display of the group a character belongs to, so long as the species has more than one.
     *
     */
    public function getGroupAttribute()
    {
        if(count($this->dropData->parameters) > 1) return ' ('.$this->parameters.')';
        else return null;
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Create drop info for a character.
     *
     * @param int              $id
     */
    public function createDrop($id, $parameters = null)
    {
        $character = Character::find($id);
        $dropData = $character->image->species->dropData;
        $drop = $this->create([
            'drop_id' => $dropData->id,
            'character_id' => $id,
            'parameters' => $parameters ? $parameters : $dropData->rollParameters(),
            'drops_available' => 0,
            'next_day' => Carbon::now()->add($dropData->data['frequency']['frequency'], $dropData->data['frequency']['interval'])->startOf($dropData->data['frequency']['interval']),
        ]);
    }
}
