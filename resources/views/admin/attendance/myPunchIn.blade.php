@extends('layouts.hr.app')

@section('title','Employee')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "My Punch In"])

<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr  style="background-color:limegreen;">
            <th colspan="3"></th>
            <th colspan="3">Punch In</th>
            <th colspan="2" style="background:red;">Punch Out</th>
            <th colspan="1"></th>
        </tr>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Name</th>
            <th scope="col">Manager</th>
            <th scope="col">IP Address</th>
            <th scope="col">Time</th>
            <th scope="col">Remarks</th>
            <th scope="col">IP Address</th>
            <th scope="col">Time</th>
            <th scope="col">Total Time</th>
        </tr>
    </thead>
    <tbody>
        @forelse($myPunchInList as $record)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{ $record->employee->first_name.' '.substr($record->employee->middle_name,0,1).' '.$record->employee->last_name }}</td>
                <td>{{ $record->employee->manager ? $record->employee->manager->first_name.' '.substr($record->employee->manager->middle_name,0,1).' '.$record->employee->manager->last_name:'--' }}</td>
                <td>{{ $record->punch_in_ip}}</td>
                <td>{{ $record->punch_in_time}}</td>
                <td>{{ $record->reason }}</td>
                <td>{{ $record->punch_out_ip }}</td>
                <td>{{ $record->punch_out_time }}</td>
                @if(!$record->punch_out_time)
                    <td style="background-color:yellow">N/A</td>   
                @else
                    <td>{{round((strtotime($record->punch_out_time) - strtotime($record->punch_in_time))/60/60)}} </td>
                @endif 
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

    $('.employee-livesearch').select2({    
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

</script>
@endsection