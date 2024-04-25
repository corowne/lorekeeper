<?php

namespace App\Models;

use App\Models\Model;
use App\Models\User\User;

class ImageBlock extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id', 'item_type', 'user_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'image_blocks';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the blocked item.
     */
    public function item()
    {
        return $this->belongsTo($item_type::find($this->item_id));
    }
    /**
     * Get the user who blocked the image.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



}