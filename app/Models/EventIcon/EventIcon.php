<?php

namespace App\Models\EventIcon;

use App\Models\Model;

class EventIcon extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alt_text', 'link', 'extension', 'image',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_icon';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;


    /**
     * Validation rules for image creation.
     *
     * @var array
     */
    public static $createRules = [
        'image'      => 'required|mimes:jpeg,jpg,gif,png,webp|max:20000',
        'link' => 'required',
        'alt_text'  => 'required',
    ];

    /**
     * Validation rules for image updating.
     *
     * @var array
     */
    public static $updateRules = [
        'link' => 'required',
        'alt_text'  => 'required',
        'is_visible' => 'required',
    ];

    /**********************************************************************************************
        ACCESSORS
    **********************************************************************************************/

/**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/eventicon';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->image;
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute() {
        return asset($this->imageDirectory.'/'.$this->imageFileName);
    }

}
