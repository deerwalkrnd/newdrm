<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
// use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;


class PasswordResetController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::whereHas('employee', function ($query) use ($request) {
            $query->where('email', $request->email);
        })->first();

        if (!$user) {
            return redirect()->route('password.forgot')->with('error', 'No user found with this email');
        }

        $token = app(PasswordBroker::class)->createToken($user);
        $status = $user->sendPasswordResetNotification($token);

        return redirect()->route('password.forgot')->with('status', 'Password reset link sent to your email');
    }

    public function showResetForm(Request $request, $token)
    {
        //check if token is present in database else response with invalid token
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6'
        ]);

        //check if token is granted to the email and created_at + 1day > now

        // else response with mismatched email and token 
        // or with expired token
    
        $user = User::whereHas('employee', function ($query) use ($request) {
            $query->where('email', $request->email);
        })->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No user found with this email');
        }else{
            $this->resetPassword($user,['password' => $request->password]);
        }
    }

    private function resetPassword($user, array $input)
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }




}
