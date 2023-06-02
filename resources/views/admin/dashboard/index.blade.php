@extends('layouts.hr.app')
@section('title','Dashboard')

@section('content')
<!-- section with top buttons start -->
<div class="topbutton_group">

    <!-- section for current time start-->
    <div class="row time_mobile">
        <div class="col current_time" id="clock1">
            time
        </div>
    </div>
    <!-- section for current time end-->

@if($holiday ?? '')
    <div class="popup">
        <div class="modal d-flex align-items-center justify-content-center show position-fixed top-0 start-0 w-100 h-100">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded shadow">
                <div class="d-flex flex-column align-items-center justify-content-center" >
                <h3 class="text-center mb-4" >Holiday on {{ date('l, d F', strtotime($holiday->date)) }}</h3>
                <div class="card mb-3" >
                    <div class="card-body">
                    <h5 class="card-title text-primary text-center fw-bold">{{$holiday->name}}</h5>
                    <p class="card-text">Enjoy your day off!</p>
                    </div>
                </div>
                <button onclick="closeHolidayPopup()" class="btn btn-primary">Close</button>
                </div>
            </div>
            </div>
        </div>
    </div>
@endif

@if ($festival ?? '')
    @if (!is_null($festival->image))
    <div class="festivalpopup">
            <div class="modal d-flex align-items-center justify-content-center show position-fixed top-0 start-0 w-100 h-100">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-4 rounded shadow">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <div class="modal-body">
                                <img src="/storage/{{$festival->image}}" class="img-fluid" alt="Festival Image">
                            </div>
                            <button onclick="closeFestivalPopup()" class="btn btn-primary">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

    <div class="row top_buttons mx-4">
        <div class="col">
            <a href="/leave-request/create"><button type="button" class="btn btn-md applyLeave_btn mb-2">Apply for Leave</button></a>
            <a href="/leave-request"><button type="button" class="btn btn-md applyLeave_btn mb-2">Leave Details</button></a>
            <a href="/myPunchIn"><button type="button" class="btn btn-md applyLeave_btn mb-2">My Punch In</button></a>
            <a href="/employee/profile"><button type="button" class="btn btn-md applyLeave_btn mb-2">My Profile</button></a>
            <a href="/leave-request/my-forced"><button type="button" class="btn btn-md applyLeave_btn mb-2">Forced Leave Details</button></a>

            @if(Auth::user()->role->authority == 'manager')
            <a href="/leave-request/approve"><button type="button" class="btn btn-md applyLeave_btn mb-2">Leave Request</button></a>
            @endif
        </div>

        <!-- new code -->
            @include('layouts.basic.punchInPunchOut')
        <!-- new code ends here -->
    </div>
</div>
<!-- section with top buttons end -->

<!-- section for current time start-->
<div class="row mx-4 time_laptop">
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
            <input type="text" placeholder="" value="{{ $leaveList->count() }}" disabled style="width:55px; text-align:center;">
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
                <td colspan="7">
                    <center>No Record Found</center>
                </td>
            </tr>
            @endforelse
        </table>
    </div>
</div>
<!-- section for employees on leave part end-->

<!-- Birthday Pop Up Notification -->
@if(!$first_login_today && date('Y-m-d H:i',strtotime(Auth::user()->last_login)) == date('Y-m-d H:i') && count($todayBirthdayList)>0)
    @foreach($todayBirthdayList as $birthdayEmployee)
        <div class="modal fade birthdayModal" id="exampleModal{{$birthdayEmployee->id}}" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content birthday-modal-content-no-photo">
                    <div cl ass="modal-body">
                        <div class="container-fluid pt-2 pb-0">
                            <div class="image-div" style="position:relative;">
                                <button type="button" class="btn text-white close-button" data-bs-dismiss="modal" aria-label="Close" >X</button>
                                <img src="{{asset('assets/images/birthdayCardNoPhoto.jpg')}}" alt="birthday card" class="birthday-card-image-no-photo">

                                <div class="employee-name-no-photo-div" style="">
                                    <span class=" ">{{ucfirst($birthdayEmployee->first_name)." ".ucfirst(substr($birthdayEmployee->middle_name,0,1))." ".ucfirst($birthdayEmployee->last_name)}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif


@endsection

@section('scripts')
<script>
    var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
    // console.log(width);
    $(document).ready(function(){
        // if(width >= 992){
            $(".birthdayModal").modal('show');
            $(".jobfairModal").modal('show');
        // }
    });

    function closeHolidayPopup() {
        document.querySelector('.popup').style.display = 'none';
    }

    function closeFestivalPopup() {
        document.querySelector('.festivalpopup').style.display = 'none';
    }
</script>
@endsection