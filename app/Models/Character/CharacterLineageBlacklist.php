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
     * Grabs the blacklist level of the character.
     * Returns true if blacklisted.
     * Returns false if greylisted.
     * Returns null if not blacklisted.
     *
     * @return boolean|null
     */
    public static function getBlacklistLevel($character)
    {
        return null;
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
            ->join('character_images', 'characters.character_image_id', '=', 'character_images.id')
            ->whereNotIn('species_id', CharacterLineageBlacklist::getBlacklistSpecies())
            ->whereNotIn('rarity_id',  CharacterLineageBlacklist::getBlacklistRarities())
            ->whereNotIn('suptype_id', CharacterLineageBlacklist::getBlacklistSubtypes())
            ->orderBy('slug')
            ->pluck('slug', 'characters.id')
            ->toArray();

        return $query;
    }
}
