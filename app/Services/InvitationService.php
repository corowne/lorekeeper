<?php namespace App\Services;

use DB;
use App\Services\Service;

use App\Models\Invitation;

class InvitationService extends Service
{

    public function generateInvitation($user)
    {
        DB::beginTransaction();

        try {
            $invitation = Invitation::create([
                'code' => $this->generateCode(),
                'user_id' => $user->id
            ]);

            return $this->commitReturn($invitation);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function useInvitation($invitation, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if($invitation->recipient_id) throw new \Exception("This invitation key has already been used.");
            
            $invitation->recipient_id = $user->id;
            $invitation->save();

            return $this->commitReturn($invitation);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function deleteInvitation($invitation)
    {
        DB::beginTransaction();

        try {
            // Check first if the invitation has been used
            if($invitation->recipient_id) throw new \Exception("This invitation has already been used."); 
            $invitation->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    private function generateCode()
    {
        $src = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        for ($i = 0; $i < 10; $i++) $code .= $src[mt_rand(0, strlen($src) - 1)];
        return $code;
    }
}