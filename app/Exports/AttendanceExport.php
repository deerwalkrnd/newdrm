<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Request;

class AttendanceExport implements FromView,ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
{
    $request = $this->data;
    
    foreach (['e', 'ed', 'sd'] as $key) {
        if ($request->get($key) === "null" || $request->get($key) === "") {
            $request[$key] = null;
        }
    }

    $employees = Employee::select('id', 'first_name', 'middle_name', 'last_name')
        ->where('contract_status', 'active');

    $employees = $employees->when(isset($request->e), function ($query) use ($request) {
        return $query->whereHas('attendances', function ($query) use ($request) {
            return $query->where('employee_id', $request->e);
        });
    })->get();

    $startDate = isset($request->sd) ? Carbon::parse($request->sd) : Carbon::now()->subDays(30);
    $endDate = isset($request->ed) ? Carbon::parse($request->ed) : Carbon::now();

    $holidays = Holiday::where('female_only', '0')->pluck('date')->toArray();
    
    // Fetch all relevant attendances in one query
    $attendances = Attendance::whereDate('punch_in_time',">=",$startDate)->whereDate('punch_in_time',"<=",$endDate->endOfDay())->get();

    $dates = collect();
    $currentDate = $startDate->copy();
    
    while ($currentDate->lte($endDate)) {
        if (!$currentDate->isWeekend() && !in_array($currentDate->format('Y-m-d'), $holidays)) {
            $dates->push($currentDate->copy());
        }
        $currentDate->addDay();
    }
    
    // Create a map of attendances by employee ID and date
    $attendanceMap = $attendances->groupBy(function($attendance) {
        $punchInTime = Carbon::parse($attendance->punch_in_time);
        return $attendance->employee_id . '_' . $punchInTime->format('Y-m-d');
    });

    $attendanceStatuses = [];

    foreach ($employees as $employee) {
        foreach ($dates as $date) {
            $key = $employee->id . '_' . $date->format('Y-m-d');
            
            $attendancesForDate = $attendanceMap->get($key);

            $attendance = $attendancesForDate && $attendancesForDate->isNotEmpty() ? $attendancesForDate->first() : null;

            if ($attendance) {
                $punchintime = Carbon::parse($attendance->punch_in_time)->format('H:i:s');
                $punchouttime = Carbon::parse($attendance->punch_out_time)->format('H:i:s');

                if ($punchintime > '13:30:00' || $punchouttime < '13:30:00') {
                    $attendanceStatuses[$employee->id][$date->format('Y-m-d')] = 'P/A' ."<br>". 'IN: ' . $punchintime ."<br>". 'OUT: ' . $punchouttime;
                } else {
                    $attendanceStatuses[$employee->id][$date->format('Y-m-d')] = 'P' . "<br>". 'IN: ' . $punchintime . "<br>".'OUT: ' . $punchouttime;
                }
            } else {
                $attendanceStatuses[$employee->id][$date->format('Y-m-d')] = 'A';
            }
        }
    }
    return view('admin.exports.report', [
        'employees' => $employees,
        'dates' => $dates,
        'attendanceStatuses' => $attendanceStatuses,
    ]);
}

}