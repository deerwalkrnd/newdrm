<?php

namespace App\Http\Middleware;
use Illuminate\Validation\Rules\Password;
use Closure;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;

class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //validate input before proceeding
        $request->validate([
            '_token' => 'required',
            'email' => 'required|email',
            // 'password' => 'required|confirmed|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|',
            'password' => ['required','confirmed',Password::min(8)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols()
                            ->uncompromised()
                        ],
            'reset_token' => 'required',
        ]);
        // dd('here');
        $query = \DB::table('password_resets')->where('email', $request->email);
    
        if($query->count() > 0) {
            $tokenDetail = $query->first();
            if(strtotime($tokenDetail->created_at) + 3600 > time()) {
                // dd("rer");
                if(\Hash::check($request->reset_token, $tokenDetail->token)) {
                    // dd("ere");
                    return $next($request);
                }
            }
        }
        return redirect()->route('password.forgot')->with('error', 'Invalid token');
    }
}
