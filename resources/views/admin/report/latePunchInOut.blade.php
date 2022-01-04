@extends('layouts.hr.app')

@section('title','Attendance')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Late Punch In/ Missed Punch Out"])

<div class="d-flex justify-content-between flex-row">
    <div class="w-25">
        <label for="date">Date: </label>
        <input type="date" name="punch_date" id="punch_date" onchange="search()" value="{{ request()->get('d') ?? request()->get('d') }}" >
    </div> 
    <div >
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288">Reset</button>
    </div>
</div>
<br>
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr >
            <th colspan="3"></th>
            <th colspan="3" style="background-color:#FFBF00">Punch In</th>
            <th colspan="1" style="background:red;">Punch Out</th>
            <!-- <th colspan="1"></th> -->
        </tr>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Name</th>
            <th scope="col">Manager</th>
            <th scope="col">IP Address</th>
            <th scope="col">Time</th>
            <th scope="col">Remarks</th>
            <th scope="col">Missed Punch Out</th>
        </tr>
    </thead>
    <tbody>
        @forelse($latePunchInOuts as $record)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{ $record->employee->first_name.' '.substr($record->employee->middle_name,0,1).' '.$record->employee->last_name }}</td>
                <td>{{ $record->employee->manager ? $record->employee->manager->first_name.' '.substr($record->employee->manager->middle_name,0,1).' '.$record->employee->manager->last_name:'--' }}</td>
                <td>{{ $record->punch_in_ip}}</td>
                <td>{{ $record->punch_in_time}}</td>
                <td>{{ $record->reason }}</td>
                <td>{{ $record->missed_punch_out }}</td>
            </tr>            
        @empty
        <tr>
            <th colspan=11 class="text-center text-dark">No  Punch In Yet</th>
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

    //Search by date or Employee
    function search(){
        let date = $('#punch_date').val();
        if(date)
            $(location).attr('href','/late-missed-punch?d='+date);
    }

    function reset(){
        $(location).attr('href','/punch-in-detail');
    }

</script>
@endsection