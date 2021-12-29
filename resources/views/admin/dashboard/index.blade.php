@extends('layouts.hr.app')

@section('title','Dashboard')

@section('content')
<!-- section with top buttons start -->
<div class="row top_buttons mx-4">
    <div class="col">
        <button type="button" class="btn btn-md applyLeave_btn mb-2">Apply for Leave</button>
        <button type="button" class="btn btn-md applyLeave_btn mb-2">Leave Details</button>
    </div>

    @if(session('punchIn') == 2)
    <div class="col">
        <span class="punch_out_container" style="position: relative;">
            <form class="punch_out_form" action="/punch-out" method="POST" onsubmit="return confirm('Do you want to punch-out?');">
                @csrf
                <input type="text" placeholder="Punch In/Out Remarks">
                <span class="punch_out_button">
                    <button>Punch Out</button>
                </span>
            </form>
        </span>
    </div>
    @endif
    @if(session('punchIn') == 1)
    <div class="col">
        <span class="punch_out_container" style="position: relative;">
            <form class="punch_out_form" action="/punch-in" method="POST">
                @csrf
                <input type="hidden" name="code" value="OXqSTexF5zn4uXSp">
                <input type="hidden" placeholder="Punch In/Out Remarks">
                <span class="punch_out_button">
                    <button>Punch In</button>
                </span>
            </form>
        </span>
    </div>
    @endif
    <!-- punch-in/punch-out col -->
</div>
<!-- section with top buttons end -->

<!-- section for current time start-->
<div class="row mx-4">
    <div class="col current_time mx-4">
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
            <input type="date" placeholder="" disabled>
        </div>
        <div class="col-md-2 mb-2 total_count_box">
            Total Count
            <input type="text" placeholder="" disabled>
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

            <tr>
                <td>
                    <center>1</center>
                </td>
                <td>Laxmi Tiwari</td>
                <td>Sick</td>
                <td>2021-12-16</td>
                <td>2021-12-16</td>
                <td>1.0</td>
                <td></td>
            </tr>
        </table>
    </div>
</div>
<!-- section for employees on leave part end-->
@endsection