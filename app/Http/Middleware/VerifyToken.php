<?php

namespace App\Http\Middleware;

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

        $query = \DB::table('password_resets')->where('email', $request->email);
    
        if($query->count() > 0) {
            $tokenDetail = $query->first();
            if(strtotime($tokenDetail->created_at) + 3600 > time()) {
                if(\Hash::check($request->reset_token, $tokenDetail->token)) {
                    return $next($request);
                }
            }
        }
        return redirect()->route('password.forgot')->with('error', 'Invalid token');
    }
}
