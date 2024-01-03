<?php

namespace App\Models\Character;

use App\Models\Model;
use App\Models\User\User;

class CharacterImageCreator extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_image_id', 'type', 'url', 'alias', 'character_type', 'user_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_image_creators';
    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the image associated with this record.
     */
    public function image() {
        return $this->belongsTo(CharacterImage::class, 'character_image_id');
    }

    /**
     * Get the user associated with this record.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Displays a link using the creator's URL.
     *
     * @return string
     */
    public function displayLink() {
        if ($this->user_id) {
            $user = User::find($this->user_id);

            return $user->displayName;
        } elseif ($this->url) {
            return prettyProfileLink($this->url);
        } elseif ($this->alias) {
            $user = User::where('alias', trim($this->alias))->first();
            if ($user) {
                return $user->displayName;
            } else {
                return '<a href="https://www.deviantart.com/'.$this->alias.'">'.$this->alias.'@dA</a>';
            }
        }
    }
}
