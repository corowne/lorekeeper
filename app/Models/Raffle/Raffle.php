<?php

namespace App\Models\Raffle;

use App\Models\Model;

class Raffle extends Model
{
    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['rolled_at'];

    /**
     * Accessors to append to the model.
     *
     * @var array
     */
    public $appends = ['name_with_group'];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_active', 'winner_count', 'group_id', 'order', 'ticket_cap',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'raffles';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the raffle tickets attached to this raffle.
     */
    public function tickets()
    {
        return $this->hasMany('App\Models\Raffle\RaffleTicket');
    }

    /**
     * Get the group that this raffle belongs to.
     */
    public function group()
    {
        return $this->belongsTo('App\Models\Raffle\RaffleGroup', 'group_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the raffle's name, linked to the raffle page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->displayName();
    }

    /**
     * Get the name of the raffle, including group name if there is one.
     *
     * @return string
     */
    public function getNameWithGroupAttribute()
    {
        return ($this->group_id ? '['.$this->group->name.'] ' : '').$this->name;
    }

    /**
     * Gets the raffle's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'raffle_tickets';
    }

    /**
     * Gets the raffle's url.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('raffles/view/'.$this->id);
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Displays the raffle's name, linked to the raffle page.
     *
     * @param mixed $asReward
     *
     * @return string
     */
    public function displayName($asReward = true)
    {
        return '<a href="'.$this->url.'" class="display-raffle">'.$this->name.($asReward ? ' (Raffle Ticket)' : '').'</a>';
    }
}
