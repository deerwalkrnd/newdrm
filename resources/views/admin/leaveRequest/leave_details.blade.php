@extends('layouts.hr.app')

@section('title','Leave Request')

@section('content')
@include('layouts.basic.tableHead',["table_title" => $table_title])

<div class="row">
    <div class="col-md-3">
        <label class="form-label" for="employee_id">Employee:</label>
        <select class="employee-livesearch form-control p-3" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --">
            @if(isset($employeeSearch))
                <option value="{{ request()->get('e') ?? request()->get('e') }}" 
                selected="selected">{{ $employeeSearch->first_name.' '.$employeeSearch->middle_name.' '.$employeeSearch->last_name }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="start_date">Start Date: </label>
        <input class="form-control p-2"  type="date" name="start_date" id="start_date" value="{{ request()->get('sd') ?? request()->get('sd') }}" >
    </div> 
     <div class="col-md-3">
        <label class="form-label" for="end_date">End Date: </label>
        <input class="form-control p-2"  type="date" name="end_date" id="end_date" value="{{ request()->get('ed') ?? request()->get('ed') }}" >
    </div> 
    <div class="col-md-1">
        <br>
        <button class="btn border-0 mt-2 text-white bg-success" onclick="search()" style="float:right;">Search</button>
    </div>
    <div class="col-md-2">
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288;float:right;">Reset</button>
    </div>
</div>
<div class="row" style="margin-bottom:-40px">
    <div class="col-md-3"></div>
    <div class="col-md-3 start_date_warning" style="visibility:hidden">
        <p class="text-danger">Please select both start date and end date. </p>
    </div>    
    <div class="col-md-3 end_date_warning" style="visibility:hidden">
        <p class="text-danger">Please select both start date and end date. </p>
    </div>  
</div>

<br><br>
<table class="unit_table mx-auto drmDataTable">
    <thead>
     <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Employee</th>
            <th scope="col">Leave Type</th>
            <th scope="col">Year</th>
            <th scope="col">Start Date</th>
            <th scope="col">End Date</th>
            <th scope="col">Days</th>
            <th scope="col">Half</th>
            <th scope="col">Reason</th>
            <th scope="col">State</th>
            <th scope="col">Manager</th>
            <th scope="col">Approved By</th>
            @if(strtolower($table_title)!='employee leave details lists')
                <th scope="col" class="text-center">Action</th>    
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($leaveRequests as $leaveRequest)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $leaveRequest->employee->first_name.' '.$leaveRequest->employee->last_name }}</td>
            <td>{{ $leaveRequest->leaveType->name }}</td>
            <td>{{ $leaveRequest->year }}</td>
            <td>{{ $leaveRequest->start_date }}</td>
            <td>{{ $leaveRequest->end_date }}</td>
            <td>{{ $leaveRequest->days * ($leaveRequest->full_leave == 1 ? 1 : 0.5) }}</td>
            <td>{{ $leaveRequest->half_leave }}</td>
            <td>{{ $leaveRequest->reason }}</td>
            <td>{{ $leaveRequest->acceptance }}</td>
            <td>{{ $leaveRequest->employee->manager ? $leaveRequest->employee->manager->first_name.' '.$leaveRequest->employee->manager->last_name:'--' }}</td>
            @if($leaveRequest->accepted_by_detail != NULL)
            <td>{{ ucfirst($leaveRequest->accepted_by_detail->first_name).' '.ucfirst($leaveRequest->accepted_by_detail->middle_name).' '.ucfirst($leaveRequest->accepted_by_detail->last_name) }}</td>
            @else
            <td> -- </td>
            @endif            
            @if(!($leaveRequest->start_date == date('Y-m-d')) && strtolower($table_title)!='employee leave details lists')
            <td class="text-center">
                
                <a href="/leave-request/edit/{{ $leaveRequest->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/leave-request/{{ $leaveRequest->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>

            @endif
        </tr>
        @empty
        <tr>
            <th colspan=12 class="text-center text-dark">No LeaveRequest Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $leaveRequests->appends(Request::except('page'))->links() }}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable({
            "bPaginate": false
        });
        $('.dataTables_filter').hide();
    })

    //  //Search leave by date 
    // function search(){
    //     let date = $('#date').val();
    //     if(date)
    //         $(location).attr('href','/leave-request/approve?d='+date);
    // }

    function reset(){
        $(location).attr('href','/leave-request/approve');
    }

    //Search by date or Employee
    function search(){
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let employee_id = $('#employee_id').val();
        // console.log(employee_id);
        if(end_date && start_date && employee_id)
            $(location).attr('href','/leave-request/details?sd='+start_date+'&ed='+end_date+'&e='+employee_id);
        else if(end_date && start_date)
            $(location).attr('href','/leave-request/details?sd='+start_date+'&ed='+end_date);
        else if(!end_date && start_date)
            $(".end_date_warning").css("visibility", "visible");
        else if(!start_date && end_date)
            $(".start_date_warning").css("visibility", "visible");
        else if(employee_id)
            $(location).attr('href','/leave-request/details?e='+employee_id);
    }

    function reset(){
        $(location).attr('href','/leave-request/details');
    }

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