<?php

namespace App\Models\Gallery;

use App\Models\Currency\Currency;
use App\Models\Model;
use Settings;

class GalleryCollaborator extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gallery_submission_id', 'user_id',
        'has_approved', 'data', 'type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gallery_submission_collaborators';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the submission this is attached to.
     */
    public function submission()
    {
        return $this->belongsTo('App\Models\Gallery\GallerySubmission', 'gallery_submission_id');
    }

    /**
     * Get the user being attached to the submission.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the display name of the participant's type.
     *
     * @return string
     */
    public function getDisplayTypeAttribute()
    {
        switch ($this->type) {
            default:
                flash('Invalid type selected.')->error();
                break;
            case 'Collab':
                return 'Collaborator';
                break;
            case 'Trade':
                return 'Trade With';
                break;
            case 'Gift':
                return 'Gift For';
                break;
            case 'Comm':
                return 'Commissioned';
                break;
            case 'Comm (Currency)':
                return 'Commissioned ('.Currency::find(Settings::get('group_currency'))->name.')';
                break;
        }
    }
}
