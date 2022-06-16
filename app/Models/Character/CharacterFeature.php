<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterFeature extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_image_id', 'feature_id', 'data', 'character_type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_features';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the image associated with this record.
     */
    public function image()
    {
        return $this->belongsTo('App\Models\Character\CharacterImage', 'character_image_id');
    }

    /**
     * Get the feature (character trait) associated with this record.
     */
    public function feature()
    {
        return $this->belongsTo('App\Models\Feature\Feature', 'feature_id');
    }
}
