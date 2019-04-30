<?php

namespace App\Helpers;

use DB;
use App\Models\Notification;

class Notifications {
    public function create($type, $user, $data)
    {
        DB::beginTransaction();

        try {
            $notification = Notification::create([
                'user_id'               => $user->id,
                'notification_type_id'  => Notification::getNotificationId($type),
                'data'                  => json_encode($data),
                'is_unread'             => 1
            ]);

            $user->notifications_unread++;
            $user->save();
            
            DB::commit();
            return true;
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        DB::rollback();
        return false;
    }
}