<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\MailControl;
use App\Helpers\MailHelper;
use App\Mail\PasswordResetNotificationMail;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function getForgotPassword(){
        return view('auth.forgotPassword');
    }

    public function postForgotPassword(Request $request){
        $request->validate(['username' => 'required|exists:users,username']);

        $user = User::where('username',$request->username)->with('employee:id,email,first_name,last_name,middle_name')->first();
        $user->password = Hash::make('Deerwa1k@DRM');

        if($user->update()){
            Mail::to($user->employee->email)
            ->send(new PasswordResetNotificationMail($user));

             $res = [
                'title' => 'Password Reset Successfull!',
                'message' => 'Your password has been successfully reset. Please check your email for updated password.',
                'icon' => 'success'
            ];
        
        };

        return redirect('/')->with(compact('res'));;
    }
}
