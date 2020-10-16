<?php

namespace App\Models\Gallery;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class GalleryFavorite extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'gallery_submission_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gallery_favorites';

    /**
     * Get the character being attached to the submission.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    /**
     * Get the submission this is attached to.
     */
    public function submission() 
    {
        return $this->belongsTo('App\Models\Gallery\GallerySubmission', 'gallery_submission_id');
    }

}
