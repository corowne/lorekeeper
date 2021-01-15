<?php namespace App\Services\Stats;

use Carbon\Carbon;
use App\Services\Service;

use DB;
use Config;
use Notifications;

use App\Models\Stats\Character\Stat;
use App\Models\Item\Item;

class StatManager extends Service
{
    public function creditUserStat($user, $type, $data, $next)
    {
        DB::beginTransaction();

        try {
            $points  = $next->stat_points;

            if($this->createTransferLog($user->id, 'User', $user->id, 'User', $type, $data, $points))
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

    /* --------------------------------
    |
    |   CHARACTER
    |
    |  -----------------------------------
    */

    public function levelCharaStat($stat, $character)
    {
        DB::beginTransaction();

        try {
            // registering previous
            $previous = $stat->stat_level;
            $stat->stat_level += 1;
            $stat->save();

            $headerStat = $stat->stat;
            if($headerStat->multiplier || $headerStat->step)
            {
                // First if there's a step, add that
                // This is so that the multiplier affects the new step total
                // E.G if the current is 10 and step is 5, we do 15 * multiplier
                // This can be changed if desired but generally I think this is fine
                if($headerStat->step)
                {
                    $stat->count += $headerStat->step;
                    $stat->save();
                }
                if($headerStat->multiplier)
                {
                    $total = $stat->count * $headerStat->multiplier;
                    $stat->count = $total;
                    $stat->save();
                }
            }

            $character->level->current_points -= 1;
            $character->level->save();

            $type =  'Stat Level Up';
            $data = 'Point used in stat level up.';
            if(!$this->createTransferLog($character->id, 'Character', $character->id, 'Character', $type, $data, -1)) throw new \Exception('Error creating log.');
            if(!$this->createLevelLog($character->id, $headerStat->id, 'Character', $previous, $stat->stat_level)) throw new \Exception('Error creating log.');
            
            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Creates a log.
     */
    public function createTransferLog($senderId, $senderType, $recipientId, $recipientType, $type, $data, $quantity)
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

    /**
     * Creates a log.
     */
    public function createLevelLog($recipientId, $stat, $recipientType, $previous, $new)
    {
        
        return DB::table('stat_log')->insert(
            [
                'recipient_id' => $recipientId,
                'stat_id' => $stat,
                'leveller_type' => $recipientType,
                'previous_level' => $previous,
                'new_level' => $new,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}