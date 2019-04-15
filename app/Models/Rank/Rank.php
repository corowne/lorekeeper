<?php

namespace App\Models\Rank;

use Config;
use App\Models\Model;

class Rank extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'sort', 'color'
    ];
    protected $table = 'ranks';
    
    public static $rules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'color' => 'nullable|regex:/^#?[0-9a-fA-F]{6}$/i'
    ];

    public function powers() 
    {
        return $this->hasMany('App\Models\Rank\RankPower');
    }

    public function getDisplayNameAttribute() 
    {
        if($this->color) return '<strong style="color: #'.$this->color.'">'.$this->name.'</strong>';
        return $this->name;
    }
    
    public function canEditRank($rank)
    {
        if(is_numeric($rank)) $rank = Rank::find($rank);
        if($this->hasPower('edit_ranks')) {
            if($this->isAdmin) {
                if($rank->id != $this->id) return 1; // can edit everything
                else return 2; // limited edit: cannot edit sort order/powers
            }
            else if ($this->sort > $rank->sort) return 1;
        }
        return 0;
    }

    public function getIsAdminAttribute()
    {
        if($this->id == Rank::orderBy('sort', 'DESC')->first()->id) return true;
        return false;
    }

    public function hasPower($power)
    {
        if($this->isAdmin) return true;
        return $this->powers()->where('power', $power)->exists(); 
    }

    public function getPowers()
    {
        if($this->isAdmin) return Config::get('lorekeeper.powers');
        $powers = $this->powers->pluck('power')->toArray();
        return array_only(Config::get('lorekeeper.powers'), $powers);
    }
}
