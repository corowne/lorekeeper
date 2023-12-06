<?php

namespace App\Actions\Fortify;

use App\Models\Invitation;
use App\Models\User\User;
use App\Services\InvitationService;
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
        Validator::make($input, [
            'name'      => ['required', 'string', 'min:3', 'max:25', 'alpha_dash', 'unique:users'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'agreement' => ['required', 'accepted'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'code'      => ['string', function ($attribute, $value, $fail) {
                if (!Settings::get('is_registration_open')) {
                    if (!$value) {
                        $fail('An invitation code is required to register an account.');
                    }
                    $invitation = Invitation::where('code', $value)->whereNull('recipient_id')->first();
                    if (!$invitation) {
                        $fail('Invalid code entered.');
                    }
                }
            },
            ],
        ])->validate();

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
