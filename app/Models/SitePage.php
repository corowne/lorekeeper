<?php

namespace App\Models;

use Config;
use App\Models\Model;

use App\Traits\Commentable;

class SitePage extends Model
{
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'title', 'text', 'parsed_text', 'is_visible', 'can_comment'
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

    /**
     * Displays the news post title, linked to the news post itself.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'">'.$this->title.'</a>';
    }
}
