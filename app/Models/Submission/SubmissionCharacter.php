<?php

namespace App\Models\Submission;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class SubmissionCharacter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'submission_id', 'character_id', 'data'
    ];
    protected $table = 'submission_characters';
    
    
    public function submission() 
    {
        return $this->belongsTo('App\Models\Submission\Submission', 'submission_id');
    }
    
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }
    
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }
}
