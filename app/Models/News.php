<?php

namespace App\Models;

use Carbon\Carbon;
use Config;
use App\Models\Model;

class News extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'text', 'parsed_text', 'title', 'is_visible', 'post_at'
    ];
    protected $table = 'news';
    public $timestamps = true;
    public $dates = ['post_at'];

    public static $createRules = [
        'title' => 'required|between:3,25',
        'text' => 'required',
    ];
    
    public static $updateRules = [
        'title' => 'required|between:3,25',
        'text' => 'required',
    ];

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }

    public function getSlugAttribute()
    {
        return $this->id . '.' . str_slug($this->title);
    }

    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'">'.$this->title.'</a>';
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    public function scopeShouldBeVisible($query)
    {
        return $query->whereNotNull('post_at')->where('post_at', '<', Carbon::now())->where('is_visible', 0);
    }


    public function getUrlAttribute()
    {
        return url('news/'.$this->slug);
    }
}
