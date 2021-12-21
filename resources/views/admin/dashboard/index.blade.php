@extends('layouts.hr.app')

@section('title','Dashboard')

@section('content')
<!-- section with top buttons start -->
<div class="row top_buttons mx-4">
    <div class="col">
        <button type="button" class="btn btn-md applyLeave_btn mb-2">Apply for Leave</button>
        <button type="button" class="btn btn-md applyLeave_btn mb-2">Leave Details</button>
    </div>

    <div class="col">
        <span class="punch_out_container" style="position: relative;">
            <form class="punch_out_form">
                <input type="text" placeholder="Punch In/Out Remarks">
                <span class="punch_out_button">
                    <button>Punch Out</button>
                </span>
            </form>
        </span>
    </div>
</div>
<!-- section with top buttons end -->

<!-- section for current time start-->
<div class="row mx-4">
    <div class="col current_time mx-4">
        time
    </div>
</div>
<!-- section for current time end-->

<!-- section for middle part start-->
<div class="row justify-content-around my-2">
    <div class="col-md-7 box_background p-3 mb-4">
        <span class="leave_table_title">Leave Balance</span>
        <div class="mt-3">
            <table class="unit_table mx-auto w-100">
                <tr class="table_title" style="background-color: #3573A3;">
                    <th>Leave Type</th>
                    <th>Accrued</th>
                    <th>Allowed</th>
                    <th>Leave Taken</th>
                    <th>Balance</th>
                </tr>
                <tr>
                    <td>Personal</td>
                    <td>5.5</td>
                    <td>6.5</td>
                    <td>5.0</td>
                    <td>1.5</td>
                </tr>
                <tr>
                    <td>Paternity</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>0.0</td>
                    <td>N/A</td>
                </tr>
                <tr>
                    <td>Personal</td>
                    <td>5.5</td>
                    <td>6.5</td>
                    <td>5.0</td>
                    <td>1.5</td>
                </tr>
                <tr>
                    <td>Paternity</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>0.0</td>
                    <td>N/A</td>
                </tr>
                <tr>
                    <td>Personal</td>
                    <td>5.5</td>
                    <td>6.5</td>
                    <td>5.0</td>
                    <td>1.5</td>
                </tr>
                <tr>
                    <td>Paternity</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>0.0</td>
                    <td>N/A</td>
                </tr>
                <tr>
                    <td>Personal</td>
                    <td>5.5</td>
                    <td>6.5</td>
                    <td>5.0</td>
                    <td>1.5</td>
                </tr>
                <tr>
                    <td>Paternity</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>0.0</td>
                    <td>N/A</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-4 box_background p-3 mb-4 birthdays_container">
        <span class="mb-2">Upcoming Birthdays</span>

        <div class="row px-3 mt-3 justify-content-around">
            <div class="col-md-1 birthdate_box">
                Jan <br>
                <b>1</b>
            </div>
            <div class="col-md-9 birthday_person">
                <h1>Sam Smith</h1>
                <h4>Sat, Jan 1</h4>
            </div>
        </div>

        <div class="row px-3 mt-3 justify-content-around">
            <div class="col-md-1 birthdate_box">
                Jan <br>
                <b>1</b>
            </div>
            <div class="col-md-9 birthday_person">
                <h1>Sam Smith</h1>
                <h4>Sat, Jan 1</h4>
            </div>
        </div>

        <div class="row px-3 mt-3 justify-content-around">
            <div class="col-md-1 birthdate_box">
                Jan <br>
                <b>1</b>
            </div>
            <div class="col-md-9 birthday_person">
                <h1>Sam Smith</h1>
                <h4>Sat, Jan 1</h4>
            </div>
        </div>

        <div class="row px-3 mt-3 justify-content-around">
            <div class="col-md-1 birthdate_box">
                Jan <br>
                <b>1</b>
            </div>
            <div class="col-md-9 birthday_person">
                <h1>Sam Smith</h1>
                <h4>Sat, Jan 1</h4>
            </div>
        </div>
    </div>
</div>
<!-- section for middle part end-->

<!-- section for employees on leave part start-->
<div class="employees_leave box_background">
    <div class="row pt-3 justify-content-around topRow_empleave">
        <div class="col-md-7 mb-2">
            <span class="leave_table_title">Employees on Leave</span>
        </div>
        <div class="col-md-2 mb-2 date_box">
            Date
            <input type="text" placeholder="">
        </div>
        <div class="col-md-2 mb-2 total_count_box">
            Total Count
            <input type="text" placeholder="">
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

            <tr>
                <td>
                    <center>1</center>
                </td>
                <td>Ritu Raj Lamsal</td>
                <td>Personal</td>
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