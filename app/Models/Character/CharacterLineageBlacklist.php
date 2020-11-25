<?php

namespace App\Models\Character;

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
     * For intellisense purposes.
     *
     * @var array
     */
    protected static $typeList = [
        "category" => "category",
        "subspecies" => "subspecies",
        "subtype" => "subtype",
    ];

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
     * Grabs all categories in the blacklist.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBlacklistCategories()
    {
        return null;
    }

    /**
     * Grabs all species in the blacklist.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBlacklistSpecies()
    {
        return null;
    }

    /**
     * Grabs all subspecies in the blacklist.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getBlacklistSubtypes()
    {
        return null;
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
}
