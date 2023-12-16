<?php

namespace App\Models\Character;

use App\Models\Currency\Currency;
use App\Models\Model;

class CharacterCurrency extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity', 'character_id', 'currency_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_currencies';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character the record belongs to.
     */
    public function character() {
        return $this->belongsTo(Character::class);
    }

    /**
     * Get the currency associated with this record.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the name of the currency formatted with the quantity owned.
     *
     * @return string
     */
    public function getNameWithQuantityAttribute() {
        return $this->currency->name.' [Owned: '.$this->quantity.']';
    }
}
