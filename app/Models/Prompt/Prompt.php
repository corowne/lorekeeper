<?php

namespace App\Models\Prompt;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;
use App\Models\Prompt\PromptCategory;

class Prompt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prompt_category_id', 'name', 'summary', 'description', 'parsed_description', 'is_active',
        'start_at', 'end_at', 'hide_before_start', 'hide_after_end'
    ];
    protected $table = 'prompts';
    public $dates = ['start_at', 'end_at'];
    
    public static $createRules = [
        'prompt_category_id' => 'nullable',
        'name' => 'required|unique:prompts|between:3,25',
        'summary' => 'nullable',
        'description' => 'nullable',
    ];
    
    public static $updateRules = [
        'prompt_category_id' => 'nullable',
        'name' => 'required|between:3,25',
        'summary' => 'nullable',
        'description' => 'nullable',
    ];
    
    
    public function category() 
    {
        return $this->belongsTo('App\Models\Prompt\PromptCategory', 'prompt_category_id');
    }
    
    public function rewards() 
    {
        return $this->hasMany('App\Models\Prompt\PromptReward', 'prompt_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)
            ->where(function($query) {
                $query->whereNull('start_at')->orWhere('start_at', '<', Carbon::now())->orWhere(function($query) {
                    $query->where('start_at', '>=', Carbon::now())->where('hide_before_start', 0);
                });
        })->where(function($query) {
                $query->whereNull('end_at')->orWhere('end_at', '>', Carbon::now())->orWhere(function($query) {
                    $query->where('end_at', '<=', Carbon::now())->where('hide_after_end', 0);
                });
        });
        
    }

    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    public function scopeSortCategory($query)
    {
        $ids = PromptCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return $query->orderByRaw(DB::raw('FIELD(prompt_category_id, '.implode(',', $ids).')'));
    }

    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    public function scopeSortStart($query, $reverse = false)
    {
        return $query->orderBy('start_at', $reverse ? 'DESC' : 'ASC');
    }
    
    public function scopeSortEnd($query, $reverse = false)
    {
        return $query->orderBy('end_at', $reverse ? 'DESC' : 'ASC');
    }
    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-prompt">'.$this->name.'</a>';
    }

    public function getUrlAttribute()
    {
        return url('world/prompts?name='.$this->name);
    }

    public function getAssetTypeAttribute()
    {
        return 'prompts';
    }
}
