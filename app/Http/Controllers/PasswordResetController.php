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
<<<<<<< HEAD
<<<<<<< HEAD
            return redirect()->route('password.forgot')->with('error', 'No user found with this email');
=======
            return redirect()->route('password.forgot')->with(['message' => 'No user found with this email', 'icon' => 'danger']);
>>>>>>> 37c6be020984f25e6fabb748dc7cffa7a55a0bdd
=======
            return redirect()->route('password.forgot');
            // ->with(['message' => 'No user found with this email', 'icon' => 'danger']);
>>>>>>> a3b350f008b1254d621037cfdacb2869e03b26f7
        }

        $token = app(PasswordBroker::class)->createToken($user);
        $status = $user->sendPasswordResetNotification($token);

<<<<<<< HEAD
<<<<<<< HEAD
        return redirect()->route('password.forgot')->with('status', 'Password reset link sent to your email');
=======
        return redirect()->to('/forgot-password')->with(['message' => 'Password reset link has been sent to your email', 'icon' => 'success']);
>>>>>>> 37c6be020984f25e6fabb748dc7cffa7a55a0bdd
=======
        return redirect()->to('/forgot-password');
        // ->with(['message' => 'Password reset link has been sent to your email', 'icon' => 'success']);
>>>>>>> a3b350f008b1254d621037cfdacb2869e03b26f7
    }

    public function showResetForm(Request $request, $token)
    {
        //check if token is present in database else response with invalid token
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
<<<<<<< HEAD
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6'
        ]);

        //check if token is granted to the email and created_at + 1day > now

        // else response with mismatched email and token 
        // or with expired token
    
=======
>>>>>>> 37c6be020984f25e6fabb748dc7cffa7a55a0bdd
        $user = User::whereHas('employee', function ($query) use ($request) {
            $query->where('email', $request->email);
        })->first();

        if (!$user) {
<<<<<<< HEAD
<<<<<<< HEAD
            return redirect()->back()->with('error', 'No user found with this email');
        }else{
            $this->resetPassword($user,['password' => $request->password]);
=======
            return redirect()->back()->with(['message' => 'No user found with this email', 'icon' => 'danger']);
=======
            return redirect()->back();
            // ->with(['message' => 'No user found with this email', 'icon' => 'danger']);
>>>>>>> a3b350f008b1254d621037cfdacb2869e03b26f7
        }else{
            if($this->resetPassword($user,['password' => $request->password]))
                return redirect('/');
                // ->with(['message' => 'Your password has been reset successfully', 'icon' => 'success']);
            else    
                return redirect()->back();
>>>>>>> 37c6be020984f25e6fabb748dc7cffa7a55a0bdd
        }
    }

    private function resetPassword($user, array $input)
    {
<<<<<<< HEAD
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }




=======
        $status = $user->update([
            'password' => \Hash::make($input['password']),
        ]);
        return $status;
    }
>>>>>>> 37c6be020984f25e6fabb748dc7cffa7a55a0bdd
}
