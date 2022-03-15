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
            return redirect()->route('password.forgot')->with(['message' => 'No user found with this email', 'icon' => 'danger']);
        }

        $token = app(PasswordBroker::class)->createToken($user);
        $status = $user->sendPasswordResetNotification($token);
        return redirect()->to('/forgot-password')->with(['message' => 'Password reset link has been sent to your email', 'icon' => 'success']);    
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
            return redirect()->back()->with(['message' => 'No user found with this email', 'icon' => 'danger']);
        }else{
            if($this->resetPassword($user,['password' => $request->password]))
                return redirect('/')->with(['message' => 'Your password has been reset successfully', 'icon' => 'success']);
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
