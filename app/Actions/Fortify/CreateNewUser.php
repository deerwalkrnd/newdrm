<?php

namespace App\Actions\Fortify;

use App\Models\User;
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
        // dd($input);
        Validator::make($input, [
            // 'organization_id' => ['required','exists:organizations,id'],
            'employee_id' => ['required','exists:employees,id','unique:users'],
            'role_id' => ['required','exists:roles,id'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class),
            ],
            // 'password' => $this->passwordRules(),
        ])->validate();
        $user=['username'=>$input['username'],
            'role_id'=>$input['role_id'],
            'employee_id'=>$input['employee_id'],
            'password_expired'=>'1', 
            'password'=>Hash::make('Deerwa1k@DRM')];
            // dd($user);
        return(User::create($user));
        // dd($user);
    }
}
