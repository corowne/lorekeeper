<?php

namespace App\Models\Character;

use App\Models\Character\Character;
use App\Models\Model;

class CharacterLineageBlacklist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'type', 'type_id', 'complete_removal',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_lineage_blacklist';

    /**
     * Returns true if this is a blacklist, false if it is a greylist.
     *
     * @var string
     */
    public function isBlacklist()
    {
        return $this->complete_removal;
    }

    /**
     * Grabs all category ids in the blacklist.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBlacklistCategories()
    {
        return CharacterLineageBlacklist::getIdList('category');
    }

    /**
     * Grabs all specie ids in the blacklist.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBlacklistSpecies()
    {
        return CharacterLineageBlacklist::getIdList('species');
    }

    /**
     * Grabs all subspecie ids in the blacklist.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBlacklistSubtypes()
    {
        return CharacterLineageBlacklist::getIdList('subtype');
    }

    /**
     * Grabs all rarity ids in the blacklist.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBlacklistRarities()
    {
        return CharacterLineageBlacklist::getIdList('rarity');
    }

    /**
     * ID grab
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getIdList($type)
    {
        return CharacterLineageBlacklist::where('type', $type)
            ->pluck('type_id')
            ->toArray();
    }

    /**
     * returns true if can have children
     *
     * @param  App\Models\Character\Character $character
     * @return boolean
     */
    public static function canHaveChildren($character)
    {
        return CharacterLineageBlacklist::getBlacklistLevel($character, 1) < 1;
    }

    /**
     * returns true if can have ancestors
     *
     * @param  App\Models\Character\Character $character
     * @return boolean
     */
    public static function canHaveLineage($character)
    {
        return CharacterLineageBlacklist::getBlacklistLevel($character, 2) < 2;
    }

    /**
     * Grabs the blacklist level of the character.
     * Returns 2 if blacklisted.
     * Returns 1 if greylisted.
     * Returns 0 if not blacklisted.
     * May optionally set a min level to look for (1 or 2)
     *
     * @param  App\Models\Character\Character $character
     * @param  int                            $level
     * @return int
     */
    public static function getBlacklistLevel($character, $maxLevel = 2)
    {
        if(!$character) return 0;
        $level = [
            $character->is_myo_slot ? 1 : 0,
            0,
            0,
            0
        ];
        $parts = [
            'category' => $character->character_category_id,
            'rarity'   => $character->rarity_id,
            'species'  => $character->image->species_id,
            'subtype'  => $character->image->subtype_id,
        ];

        $i = 0;
        foreach ($parts as $type => $id) {
            if (max($level) == $maxLevel) return $maxLevel;
            if (max($level) > 1) return 2;

            $entry = CharacterLineageBlacklist::where('type', $type)->where('type_id', $id)->get()->first();
            if ($entry) $level[$i] = $entry->complete_removal ? 2 : 1;

            $i++;
        }
        return max($level);
    }

    /**
     * Grabs the list of characters that can show up in dropdowns.
     * Returns ids and slugs, ready for dropdown display.
     *
     * @return array
     */
    public static function getAncestorOptions()
    {
        // JOIN
        $query = Character::query()
            ->where('is_myo_slot', false)
            ->whereNotIn('character_category_id', CharacterLineageBlacklist::getBlacklistCategories())
            ->whereNotIn('rarity_id',  CharacterLineageBlacklist::getBlacklistRarities())
            ->join('character_images', 'characters.character_image_id', '=', 'character_images.id')
            ->whereNotIn('species_id', CharacterLineageBlacklist::getBlacklistSpecies())
            ->whereNotIn('suptype_id', CharacterLineageBlacklist::getBlacklistSubtypes())
            ->orderBy('slug')
            ->pluck('slug', 'characters.id')
            ->toArray();

        return $query;
    }

    /**
     * Adds, update or removes a blacklist entry for the specified type and ID.
     *
     * @param  int     $level
     * @param  string  $type
     * @param  int     $typeID
     *
     * @return App\Models\Character\CharacterLineageBlacklist|null
     */
    public static function searchAndSet($level, $type, $typeID)
    {
        $blacklistEntry = CharacterLineageBlacklist::where('type', $type)->where('type_id', $typeID)->get()->first();
        $blacklist = false;
        if(isset($level)) $blacklist = ($level == 1 || $level == 2);

        if($blacklist) {
            // should have a blacklist, search and create or update
            if($blacklistEntry) {
                $blacklistEntry->complete_removal = ($level == 2);
                $blacklistEntry->save();
            } else {
                $blacklistEntry = CharacterLineageBlacklist::create([
                    'type' => $type,
                    'type_id' => $typeID,
                    'complete_removal' => ($level == 2),
                ], false);
            }
            return $blacklistEntry;
        } else {
            // should have no blacklist, search and destroy
            if($blacklistEntry) $blacklistEntry->delete();
            return null;
        }
    }
}
