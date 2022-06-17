@extends('layouts.hr.app')

@section('title','Forced Leave Request')

@section('content')
@include('layouts.basic.tableHead',["table_title" => 'My Forced Leave'])
@if((\Auth::user()->role->authority == 'hr'))

<div class="d-flex justify-content-between flex-row">
    <div class="w-25">
        <a href="/leave-request/forced"><button class="btn border-0 text-white" style="background-color:#0f5288">Employee's Forced Leave</button></a> 
    </div> 
</div>
<br>
@endif
<table class="unit_table mx-auto drmDataTable">
    <thead>
     <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Employee</th>
            <th scope="col">Leave Type</th>
            <th scope="col">Year</th>
            <th scope="col">Date</th>
            <th scope="col">Days</th>
            <th scope="col">Reason</th>
        </tr>
    </thead>
    <tbody>
        @forelse($leaveList as $key=>$leave)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $leaveList->firstItem() + $key }}</th>
            <td>{{ $leave->employee->first_name.' '.$leave->employee->last_name }}</td>
            <td>{{ $leave->leaveType->name }}</td>
            <td>{{ $leave->year }}</td>
            <td>{{ $leave->end_date }}</td>
            <td>{{ $leave->days * ($leave->full_leave == 1 ? 1 : 0.5) }}</td>
            <td>{{ $leave->reason }}</td>
        </tr>
        @empty
        <tr>
            <th colspan=12 class="text-center text-dark">No Force Leave Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $leaveList->links() }}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable({
            "bPaginate": false
        });
    })

     //Search leave by date 
    function search(){
        let date = $('#date').val();
        if(date)
            $(location).attr('href','/leave-request/approve?d='+date);
    }

    function reset(){
        $(location).attr('href','/leave-request/approve');
    }


    //Search by date or Employee
    // function search(){
    //     let date = $('#punch_date').val();
    //     let employee_id = $('#employee_id').val();
    //     console.log(employee_id);
    //     if(date)
    //         $(location).attr('href','/punch-in-detail?d='+date);
    //     if(employee_id)
    //         $(location).attr('href','/punch-in-detail?e='+employee_id);

    // }

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