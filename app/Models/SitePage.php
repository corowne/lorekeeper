<?php

namespace App\Models;

use Config;
use DB;
use App\Models\Model;

class SitePage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'title', 'text', 'parsed_text', 'is_visible', 'page_category_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'site_pages';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'page_category_id' => 'nullable',
        'key' => 'required|unique:site_pages|between:3,25|alpha_dash',
        'title' => 'required|between:3,25',
        'text' => 'nullable',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'page_category_id' => 'nullable',
        'key' => 'required|between:3,25|alpha_dash',
        'title' => 'required|between:3,25',
        'text' => 'nullable',
    ];

    /**
     * Gets the URL of the public-facing page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('info/'.$this->key);
    }

     /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the category the page belongs to.
     */
    public function category() 
    {
        return $this->belongsTo('App\Models\SitePageCategory', 'page_category_id');
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/
    
    /**
     * Scope a query to sort pages in alphabetical order.
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
     * Scope a query to sort page in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        $ids = SitePageCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(page_category_id, '.implode(',', $ids).')')) : $query;
    }

}
