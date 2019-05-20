<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\User\User;
use App\Models\Character\CharacterCategory;

class CharacterImageCreator extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_image_id', 'type', 'url', 'alias'
    ];
    protected $table = 'character_image_creators';
    public $timestamps = true;
    
    
    public function image() 
    {
        return $this->belongsTo('App\Models\Character\CharacterImage', 'character_image_id');
    }
    
    public function displayLink()
    {
        if ($this->url)
        {
            return '<a href="'.$this->url.'" class="display-creator">'.$this->alias ? : $this->url.'</a>';
        }
        else if($this->alias)
        {
            $user = User::where('alias', $this->alias)->first();
            if($user) return $user->displayName;
            else return '<a href="https://www.deviantart.com/'.$this->alias.'">'.$this->alias.'@dA</a>';
        }
    }
}
