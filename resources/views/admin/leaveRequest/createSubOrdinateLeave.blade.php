@extends('layouts.hr.app')

@section('title','Sub-Ordinate Leave')

@section('content')
<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Create Sub-Ordinate Leave</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
    <form method="POST"class="main_form p-4" class="main_form p-4" action="/leave-request/subordinate-leave">
        @csrf
            <label for="employee_id">Employee Name*</label>
            <select class="manager-livesearch form-control p-3 mb-2" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --"></select>
            @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        <!-- choose employee -->
        @include('admin.leaveRequest._form')
        <center><button type="submit" class="btn btn-primary mt-2">Add</button></center>   
        </form>
    </div>
</section>
<!-- form end -->
@endsection

@section('scripts')
<script>
     $('.manager-livesearch').select2({    
        ajax: {
            url: '/employee/search',
            data: function (params) {
                var query = {
                    q: params.term,
                }
                    // Query parameters will be ?search=[term]
                return query;
            },
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        let full_name = (item.middle_name === null) ? item.first_name + " " + item.last_name : item.first_name + " " + item.middle_name + " " + item.last_name;
                        return {
                            text: full_name,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        }
    });
    
    //calculate leave days
    function calculateLeaveDays(){
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;
        var leave_type_id = document.getElementById('leave_type_id').value;
        var leave_times = document.getElementsByName('leave_time');
        var reason = document.getElementById('reason');
        reason.style.visibility = "hidden";
        var leave_time;


        leave_times.forEach((leaveTime)=>{
            if(leaveTime.checked){
                leave_time = leaveTime.value;
            }
        });
        // console.log('leave time is:',leave_time);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "/calculate-leave-days",
            data: {
                "_token": "{{ csrf_token() }}",
                "start_date":start_date, 
                "end_date":end_date, 
                "leave_type_id":leave_type_id,
                "leave_time":leave_time,
                },
            dataType:'json',
            success: function(data) {
                document.getElementById('days').setAttribute('value',data.days);
                if(data.reason){
                    reason.innerHTML = data.reason;
                    reason.style.visibility = "visible";
                }

                console.log('in create form',data.days,data.reason);
            },
             error: function (data) {
                console.log(data);
            }
        });
    }

</script>
@endsection