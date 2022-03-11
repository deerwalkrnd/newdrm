<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsLoggedIn
{
    private $userRoles = ['hr', 'manager', 'employee'];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(\Auth::user()){
            if(in_array(\Auth::user()->role->authority, $this->userRoles) && (\Auth::user()->employee->contract_status == 'active'))
            {
                return $next($request);
            }else{
                \Auth::logout();
                \Session::flush();
                return \Redirect::to('/login');
            }
        }else{
            return redirect('/login');
        }
    }
}
