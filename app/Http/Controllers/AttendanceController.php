<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Exports\TerminatedAttendanceExport;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Attendance;
use App\Models\MailControl;
use App\Models\LeaveRequest;
use App\Models\NoPunchInNoLeave;
use App\Helpers\MailHelper;
use App\Helpers\NepaliCalendarHelper;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Time;
use Carbon\Carbon;
use App\Mail\LatePunchInMail;
use App\Mail\EarlyPunchOutMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    private $redirect_to = '/dashboard';
    private $verificationCode = 'OXqSTexF5zn4uXSp';

    /*
    |--------------------------------------------------------------------------
    | FIX: CENTRALIZED IP HANDLER
    |--------------------------------------------------------------------------
    */
    private function getClientIp()
    {
        return request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->getClientIp()
            ?? request()->ip();
    }

    private function recordRowExists($employee_id)
    {
        $today = date('Y-m-d');
        return Attendance::where('employee_id', $employee_id)
            ->whereDate('punch_in_time', $today)
            ->count() > 0;
    }

    private function hasPunchOut($employee_id)
    {
        $today = date('Y-m-d');
        $punchOutTime = Attendance::select('punch_out_time')
            ->where('employee_id', $employee_id)
            ->whereDate('punch_in_time', $today)
            ->first();

        return $punchOutTime && $punchOutTime->punch_out_time !== null;
    }

    public function index()
    {
        $state = 1;

        if ($this->recordRowExists(\Auth::user()->employee_id)) {
            $state = 2;
            if ($this->hasPunchOut(\Auth::user()->employee_id)) {
                $state = 3;
            }
        }

        \Session::put('punchIn', $state);

        if ($state == 1) {
            return view('admin.attendance.punchIn')
                ->with(['code' => $this->verificationCode]);
        }

        return redirect($this->redirect_to);
    }

    public function punchIn(Request $request)
    {
        $successful = [
            'title' => 'Employee Punched In',
            'message' => 'Employee has been successfully Punched In',
            'icon' => 'success'
        ];

        $failed = [
            'title' => 'Employee Punch In Failed',
            'message' => 'Employee cannot be Punched In',
            'icon' => 'warning'
        ];

        if ($request->id) {
            $employee_id = $request->id;

            $exists = NoPunchInNoLeave::where('employee_id', $employee_id)->exists();

            if ($exists) {
                $res = [
                    'title' => 'Employee Punch In Failed',
                    'message' => 'No Punch-In No Leave record exists',
                    'icon' => 'warning'
                ];
                return redirect('/employee')->with(compact('res'));
            }

            $state = 1;
            $request->merge(['reason' => 'HR Punch In']);

        } else {
            $state = 2;
            $employee_id = \Auth::user()->employee_id;
        }

        $attendance = $this->takeAttendance($employee_id, $request, $state);

        $res = $attendance ? $successful : $failed;

        return redirect($this->redirect_to)->with(compact('res'));
    }

    public function takeAttendance($employee_id, $request, $state)
    {
        if ($this->recordRowExists($employee_id)) return false;

        if ($request->code != $this->verificationCode) return false;

        $presentTime = Carbon::now()->format('Y-m-d');
        $punch_in_time = Carbon::now()->toDateTimeString();

        $maxTime = Time::where('id', '1')->first()->time ?? '09:00:00';
        $maxTime = strtotime(date('Y-m-d') . ' ' . $maxTime);

        $isLate = strtotime(Carbon::now()) > $maxTime ? '1' : '0';

        $ip = $this->getClientIp();

        $attendance = Attendance::create([
            'employee_id' => $employee_id,
            'punch_in_time' => $punch_in_time,
            'punch_in_ip' => $ip,
            'late_punch_in' => $isLate,
            'reason' => $request->reason ?? null
        ]);

        \Session::put('punchIn', $state);

        return true;
    }

    public function punchOut(Request $request)
    {
        $employee_id = \Auth::user()->employee_id;
        $today = date('Y-m-d');

        if ($this->recordRowExists($employee_id) && !$this->hasPunchOut($employee_id)) {

            $ip = $this->getClientIp();

            Attendance::where('employee_id', $employee_id)
                ->whereDate('punch_in_time', $today)
                ->update([
                    'punch_out_time' => Carbon::now()->toDateTimeString(),
                    'punch_out_ip' => $ip,
                ]);

            \Session::put('punchIn', '3');
        }

        return redirect($this->redirect_to);
    }

    /*
    |--------------------------------------------------------------------------
    | FIXED FORCE PUNCH OUT (NO HARDCODED IP)
    |--------------------------------------------------------------------------
    */
    public function ForcePunchOut()
    {
        $ip = $this->getClientIp();

        $attendances = Attendance::whereDate('punch_in_time', date('Y-m-d'))
            ->whereNull('punch_out_time')
            ->update([
                'punch_out_time' => date('Y-m-d H:i:s'),
                'punch_out_ip' => $ip
            ]);

        return response()->json([
            'success' => $attendances > 0,
            'message' => $attendances > 0
                ? "Punch Out Successfully."
                : "Everyone already punched out."
        ]);
    }
}
