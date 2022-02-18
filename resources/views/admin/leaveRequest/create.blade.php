@extends('layouts.hr.app')

@section('title','Leave Request')

@section('content')
<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Create Leave Request</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
    <form class="main_form p-4" method="POST" action="/leave-request">
        @csrf
        @include('admin.leaveRequest._form')
        <center><button type="submit" class="btn btn-primary mt-2">Add</button></center>   
        </form>
    </div>
</section>
<!-- form end -->
@endsection


@section('scripts')
<script>
    
    
    function calculateLeaveDays(){
        var start_date = new Date(document.getElementById('start_date').value);
        var end_date = new Date(document.getElementById('end_date').value);


        // $.ajax

        // var days = document.getElementById('days').value;
        // var leave_days;

        // if(start_date != '' && end_date != ''){
        //     leave_days = (end_date - start_date)/(1000 * 3600 * 24) + 1;
        // }
        // document.getElementById('days').innerHTML = leave_days;
        // console.log(leave_days);
    }

</script>
@endsection