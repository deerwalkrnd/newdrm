@extends('layouts.hr.app')

@section('title','Dashboard')

@section('content')
<!-- section with top buttons start -->
<div class="row top_buttons mx-4">
    <div class="col">
        <a href="/leave-request/create"><button type="button" class="btn btn-md applyLeave_btn mb-2">Apply for Leave</button></a>
        <a href="/leave-request"><button type="button" class="btn btn-md applyLeave_btn mb-2">Leave Details</button></a>
        <a href="/leave-request/my-forced"><button type="button" class="btn btn-md applyLeave_btn mb-2">Forced Leave Details</button></a>
        <a href="/myPunchIn"><button type="button" class="btn btn-md applyLeave_btn mb-2">My Punch In</button></a>
        <a href="/employee/profile"><button type="button" class="btn btn-md applyLeave_btn mb-2">My Profile</button></a>

        @if(Auth::user()->role->authority == 'manager')
            <a href="/leave-request/approve"><button type="button" class="btn btn-md applyLeave_btn mb-2">Leave Request</button></a>
        @endif
    </div>

    @if(in_array(session('userIp'), explode(',',env('IP')) ))
        @if(session('punchIn') == 2)
        <div class="col">
            <span class="punch_out_container" style="position: relative;">
                <form class="punch_out_form" action="/punch-out" method="POST" onsubmit="return confirm('Do you want to punch-out?');">
                    @csrf
                    <input type="hidden" placeholder="Punch In/Out Remarks">
                    <span class="punch_out_button">
                        <button>Punch Out</button>
                    </span>
                </form>
            </span>
        </div>
        @endif
        @if(session('punchIn') == 1)
        <div class="col">
            <div class="row">
                <span class="punch_out_container" style="position: relative;">
                    <form class="punch_out_form" action="/punch-in" method="POST">
                        @csrf
                        <input type="hidden" name="code" value="OXqSTexF5zn4uXSp">
                        <p class="text-white">{{ session('userIp') }} | {{ env('IP') }}</p>
                        @if(session('noPunchInNoLeaveRecords') == 0)
                            @if(session('isLate') == 1)
                                @if(session('late_within_ten_days') > 0 )
                                    <p class="text-danger">Multiple Late Punch In. Please Contact HR.</p>
                                @else
                                <input placeholder="Punch In/Out Remarks" name="reason">
                                <span class="punch_out_button">
                                    <button>Punch In</button>
                                </span> 
                                @endif
                            @else
                            <span class="punch_out_button">
                                <button>Punch In</button>
                            </span>    
                            @endif
                        @else
                        <p class="text-danger">Missed Punch In and No Leave Request Record Exists. Please Contact HR.</p>
                        @endif
                    </form>
                </span>
            </div>
            <div class="row">
                <span class="punch_out_container" style="position: relative;">
                    <form class="punch_out_form">
                    @error('reason')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                    </form>
                </span>
            </div>
        </div>
        @endif
        <!-- punch-in/punch-out col -->
    @endif
</div>
<!-- section with top buttons end -->

<!-- section for current time start-->
<div class="row mx-4">
    <div class="col current_time mx-4" id="clock">
        time
    </div>
</div>
<!-- section for current time end-->

@include('admin.dashboard.midSection')


<!-- section for employees on leave part start-->
<div class="employees_leave box_background">
    <div class="row pt-3 justify-content-around topRow_empleave">
        <div class="col-md-7 mb-2">
            <span class="leave_table_title">Employees on Leave</span>
        </div>
        <div class="col-md-2 mb-2 date_box">
            Date
            <input type="date" placeholder="" disabled value="{{date('Y-m-d')}}" style="width:150px; text-align:center;">
        </div>
        <div class="col-md-2 mb-2 total_count_box">
            Total Count
            <input type="text" placeholder="" value="{{ $leaveList->count() }}" disabled  style="width:55px; text-align:center;">
        </div>
    </div>
    <div class="row empOnLeave_table_container mx-3">
        <table class="empOnLeave_table table">
            <tr>
                <th>
                    <center>S.N.</center>
                </th>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Leave Days</th>
                <th>Half</th>
            </tr>
            @forelse($leaveList as $onLeave)
            <tr>
                <td>
                    <center>{{ $loop->iteration }}</center>
                </td>
                <td>{{ $onLeave->employee->first_name." ".$onLeave->employee->last_name}}</td>
                <td>{{ $onLeave->leaveType->name }}</td>
                <td>{{ $onLeave->start_date }}</td>
                <td>{{ $onLeave->end_date }}</td>
                <td>{{ $onLeave->days * ($onLeave->full_leave == 1 ? 1 : 0.5) }}</td>
                <td>{{ $onLeave->full_leave == 0 ? $onLeave->half_leave : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7"><center>No Record Found</center></td>
            </tr>
            @endforelse
        </table>
    </div>
</div>
<!-- section for employees on leave part end-->
@endsection