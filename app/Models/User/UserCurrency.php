<?php

namespace App\Models\User;

use App\Models\Currency\Currency;
use App\Models\Model;

class UserCurrency extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity', 'user_id', 'currency_id',
    ];

    /**
     * Accessors to append to the model.
     *
     * @var array
     */
    protected $appends = [
        'name_with_quantity',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_currencies';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who owns the currency.
     */
    public function user() {
        return $this->belongsTo(User::class);
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
     * Displays the currency's name and owned quantity.
     *
     * @return string
     */
    public function getNameWithQuantityAttribute() {
        return $this->currency->name.' [Owned: '.$this->quantity.']';
    }
}
