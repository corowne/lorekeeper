<?php

namespace App\Actions\Fortify;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\Invitation;
use App\Models\User\User;
use App\Services\InvitationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Settings;

class CreateNewUser implements CreatesNewUsers {
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @return \App\Models\User
     */
    public function create(array $input) {
        (new RegisterController)->validator($input)->validate();

        $user = User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
            'rank_id'  => 2,
        ]);

        if (isset($input['code'])) {
            if (!(new InvitationService)->useInvitation(Invitation::where('code', $input['code'])->whereNull('recipient_id')->first(), $user)) {
                throw new \Exception('An error occurred while using the invitation code.');
            }
        }

        return $user;
    }
}
