@extends('layouts.hr.app')

@section('title','No Punch In No Leave Report')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "No Punch In No Leave Report"])

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
            <th scope="col">Manager</th>
            <th scope="col">Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($noRecordList as $record)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{ $record->first_name.' '.substr($record->middle_name,0,1).' '.$record->last_name }}</td>
                @if($record->manager != NULL)
                    <td>{{ $record->manager->first_name.' '.substr($record->manager->middle_name,0,1).' '.$record->manager->last_name }}</td>
                @else
                    <td> -- </td>
                @endif
                <td>{{ $date }}</td>
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
            $(location).attr('href','/no-punch-in-leave?d='+date);
    }

    function reset(){
        $(location).attr('href','/no-punch-in-leave');
    }
</script>
@endsection