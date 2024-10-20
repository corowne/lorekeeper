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
        'start_at', 'end_at', 'hide_before_start', 'hide_after_end', 'has_image', 'prefix',
        'hide_submissions', 'level_req'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prompts';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['start_at', 'end_at'];

    /**
     * Validation rules for character creation.
     *
     * @var array
     */
    public static $createRules = [
        'prompt_category_id' => 'nullable',
        'name' => 'required|unique:prompts|between:3,100',
        'prefix' => 'nullable|unique:prompts|between:2,10',
        'summary' => 'nullable',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'prompt_category_id' => 'nullable',
        'name' => 'required|between:3,100',
        'prefix' => 'nullable|between:2,10',
        'summary' => 'nullable',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the category the prompt belongs to.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Prompt\PromptCategory', 'prompt_category_id');
    }

    /**
     * Get the rewards attached to this prompt.
     */
    public function rewards()
    {
        return $this->hasMany('App\Models\Prompt\PromptReward', 'prompt_id');
    }

    /**
     * Get the rewards attached to this prompt.
     */
    public function expreward()
    {
        return $this->hasOne('App\Models\Prompt\PromptExpReward', 'prompt_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include active prompts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

    /**
     * Scope a query to sort prompts in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort prompts in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        $ids = PromptCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(prompt_category_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Scope a query to sort features by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort prompts by start date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortStart($query, $reverse = false)
    {
        return $query->orderBy('start_at', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort prompts by end date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortEnd($query, $reverse = false)
    {
        return $query->orderBy('end_at', $reverse ? 'DESC' : 'ASC');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-prompt">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/prompts';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('prompts/prompts?name='.$this->name);
    }

    /**
     * Gets the prompt's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'prompts';
    }
}
