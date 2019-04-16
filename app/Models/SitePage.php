<?php

namespace App\Models;

use Config;
use App\Models\Model;

class SitePage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'title', 'text', 'parsed_text', 'is_visible'
    ];
    protected $table = 'site_pages';
    public $timestamps = true;
    
    public static $createRules = [
        'key' => 'required|unique:site_pages|between:3,25|alpha_dash',
        'title' => 'required|between:3,25',
        'text' => 'nullable',
    ];
    
    public static $updateRules = [
        'key' => 'required|between:3,25|alpha_dash',
        'title' => 'required|between:3,25',
        'text' => 'nullable',
    ];


    public function getUrlAttribute()
    {
        return url('info/'.$this->key);
    }
}
