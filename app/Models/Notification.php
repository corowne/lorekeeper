<?php

namespace App\Models;

use Config;
use App\Models\Model;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'notification_type_id', 'is_unread', 'data'
    ];
    protected $table = 'notifications';
    public $timestamps = true;

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }

    public function recipient() 
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    public function getMessageAttribute()
    {
        $notification = Config::get('lorekeeper.notifications.'.$this->notification_type_id);

        $message = $notification['message'];

        // Replace the URL... 
        $message = str_replace('{url}', url($notification['url']), $message);

        // Replace any variables in data... 
        $data = $this->data;
        if($data && count($data)) {
            foreach($data as $key => $value) {
                $message = str_replace('{'.$key.'}', $value, $message);
            }
        }

        return $message;
    }

    public static function getNotificationId($type)
    {
        return constant('self::'. $type);
    }

    const CURRENCY_GRANT                  = 0;
    const ITEM_GRANT                      = 1;
    const CURRENCY_REMOVAL                = 2;
    const ITEM_REMOVAL                    = 3;
    const CURRENCY_TRANSFER               = 4;
    const ITEM_TRANSFER                   = 5;
    const FORCED_ITEM_TRANSFER            = 6;
    const CHARACTER_UPLOAD                = 7;
    const CHARACTER_CURRENCY_GRANT        = 8;
    const CHARACTER_CURRENCY_REMOVAL      = 9;
    const CHARACTER_PROFILE_EDIT          = 10;
    const IMAGE_UPLOAD                    = 11;
    const CHARACTER_TRANSFER_RECEIVED     = 12;
    const CHARACTER_TRANSFER_REJECTED     = 13;
    const CHARACTER_TRANSFER_CANCELED     = 14;
    const CHARACTER_TRANSFER_DENIED       = 15;
    const CHARACTER_TRANSFER_ACCEPTED     = 16;
    const CHARACTER_TRANSFER_APPROVED     = 17;
    const CHARACTER_SENT                  = 18;
    const CHARACTER_RECEIVED              = 19;
    const SUBMISSION_APPROVED             = 20;
    const SUBMISSION_REJECTED             = 21;
}
