@extends('layouts.hr.app')

@section('title','Employee')

@section('content')


<div class="d-flex justify-content-between flex-row">
    <div class="w-25">
        <label for="date">Date: </label>
        <input type="date" name="date" id="date" onchange="search()" value="{{ request()->get('d') ?? request()->get('d') }}" >
    </div> 
    <div >
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288">Reset</button>
    </div>
</div>
<br>
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Employee</th>
            <th scope="col">Leave Type</th>
            <th scope="col">From Date</th>
            <th scope="col">To Date</th>
            <th scope="col">Leave Days</th>
            <th scope="col">Half</th>
            <th scope="col">Leave Reason</th>
        </tr>
    </thead>
    <tbody>
        @forelse($acceptedRequests as $acceptedRequest)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{ $acceptedRequest->employee->first_name.' '.substr($acceptedRequest->employee->middle_name,0,1).' '.$acceptedRequest->employee->last_name }}</td>
                <td>{{ $acceptedRequest->leaveType->name}}</td>
                <td>{{ $acceptedRequest->start_date}}</td>
                <td>{{ $acceptedRequest->end_date}}</td>
                <td>{{ $acceptedRequest->days}}</td>
                <td>{{ $acceptedRequest->half_leave ?? $acceptedRequest->half_leave}}</td>
                <td>{{ $acceptedRequest->reason}}</td>
            </tr>          
        @empty
        <tr>
            <th colspan=11 class="text-center text-dark">No Any Accepted Leave Requests Yet</th>
        </tr>
        @endforelse
    </tbody>  
</table> 
@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    });

    //Search leave by date 
    function search(){
        let date = $('#date').val();
        if(date)
            $(location).attr('href','/employees-on-leave?d='+date);
    }

    function reset(){
        $(location).attr('href','/employees-on-leave');
    }
</script>
@endsection