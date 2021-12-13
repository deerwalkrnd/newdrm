<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;

class ProcessManagerRequest
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
        //get employee count and set name
        $mCount = Employee::where('id',$request->employee_id)->count();
        if($mCount > 0)
        {
            $employee = Employee::select('id','first_name','last_name','middle_name')->findOrFail($request->employee_id);
            $full_name = ($employee->middle_name === NULL) ? $employee->first_name . " " . $employee->last_name : $employee->first_name . " " . $employee->middle_name . " " . $employee->last_name;
            $request->request->add(['employee_name' => $full_name]);
        }

        return $next($request);
    }
}
