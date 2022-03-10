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
        $user = User::whereHas('employee', function ($query) use ($request) {
            $query->where('email', $request->email);
        })->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No user found with this email');
        }else{
            if($this->resetPassword($user,['password' => $request->password]))
                return redirect('/');
            else    
                return redirect()->back();
        }
    }

    private function resetPassword($user, array $input)
    {
        $status = $user->update([
            'password' => \Hash::make($input['password']),
        ]);
        return $status;
    }
}
