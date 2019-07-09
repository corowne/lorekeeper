<?php namespace App\Services;

use DB;
use App\Notify;
use Carbon\Carbon;
use App\Services\Service;
use App\Models\Raffle\RaffleGroup;
use App\Models\Raffle\Raffle;

// handles admin side raffle creation
class RaffleService  extends Service {

    public function createRaffle($data)
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $raffle = Raffle::create(array_only($data, ['name', 'is_active', 'winner_count', 'group_id', 'order']));
        DB::commit();
        return $raffle;
    }

    public function updateRaffle($data, $raffle) 
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $raffle->update(array_only($data, ['name', 'is_active', 'winner_count', 'group_id', 'order']));
        DB::commit();
        return $raffle;
    }   

    public function createRaffleGroup($data)
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $group = RaffleGroup::create(array_only($data, ['name', 'is_active']));
        DB::commit();
        return $group;
    }

    public function updateRaffleGroup($data, $group) 
    {
        DB::beginTransaction();
        if(!isset($data['is_active'])) $data['is_active'] = 0;
        $group->update(array_only($data, ['name', 'is_active']));
        DB::commit();
        return $group;
    }   

    public function deleteRaffle($raffle) 
    {
        foreach($raffle->tickets as $ticket) $ticket->delete();
        $raffle->delete();
        return true;
    }   
    public function deleteRaffleGroup($group) 
    {
        foreach($group->raffles as $raffle) $raffle->update(['group_id' => null]);
        $group->delete();
        return true;
    }   
}
