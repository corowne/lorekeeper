<?php

namespace App\Actions\Fortify;

use Settings;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'min:3', 'max:25', 'alpha_dash', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'agreement' => ['required', 'accepted'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code' => ['string', function ($attribute, $value, $fail) {
                    if(!Settings::get('is_registration_open')) {
                        if(!$value) $fail('An invitation code is required to register an account.');
                        $invitation = Invitation::where('code', $value)->whereNull('recipient_id')->first();
                        if(!$invitation) $fail('Invalid code entered.');
                    }
                }
            ]
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
