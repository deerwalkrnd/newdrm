<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsHR
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
        if(\Auth::user())
        {
            if(\Auth::user()->role->authority == 'hr')
            {
                return $next($request);
            }
        }

        return redirect('/login');
    }
}
