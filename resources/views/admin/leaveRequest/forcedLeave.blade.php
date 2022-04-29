@extends('layouts.hr.app')

@section('title','Forced Leave Request')

@section('content')
@include('layouts.basic.tableHead',["table_title" => 'Employee Forced Leave'])

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
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($leaveList as $leave)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $leave->employee->first_name.' '.$leave->employee->last_name }}</td>
            <td>{{ $leave->leaveType->name }}</td>
            <td>{{ $leave->year }}</td>
            <td>{{ $leave->end_date }}</td>
            <td>{{ $leave->days * ($leave->full_leave == 1 ? 1 : 0.5) }}</td>
            <td>{{ $leave->reason }}</td>
            <td>
                <form action="/leave-request/force/{{ $leave->id }}" method="POST" class="d-inline" onsubmit="return confirm('Do you want to delete?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
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
        // $(document).ready(function() {
        //     $('.drmDataTable').DataTable();
        // })
        $('.drmDataTable').DataTable({
            "bPaginate": false,
            "searching":true
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