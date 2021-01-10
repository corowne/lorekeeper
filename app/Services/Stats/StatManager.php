<?php namespace App\Services\Stats;

use App\Services\Service;

use DB;
use Config;
use Carbon\Carbon;

use App\Models\Stats\Character\Stat;
use App\Models\Item\Item;

class StatManager extends Service
{
    public function creditUserStat($user, $type, $data, $next)
    {
        DB::beginTransaction();

        try {
            $points  = $next->stat_points;

            if($this->createLog($user->id, 'User', $user->id, 'User', $type, $data, $points))
            {
                $user->level->current_points += $points;
                $user->level->save();
            }
            else {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Creates a log.
     */
    public function createLog($senderId, $senderType, $recipientId, $recipientType, $type, $data, $quantity)
    {
        
        return DB::table('stat_transfer_log')->insert(
            [
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'recipient_id' => $recipientId,
                'recipient_type' => $recipientType,
                'log' => $type . ($data ? ' (' . $data . ')' : ''),
                'log_type' => $type,
                'data' => $data,
                'quantity' => $quantity,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}