@extends('layouts.hr.app')

@section('title','Employee')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Employee Punch In-Out Detail"])


<div class="row">
    <div class="col-md-4">
        <label class="form-label" for="employee_id">Employee:</label>
        <select class="employee-livesearch form-control p-3" onchange="search()"  name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --">
            @if(!empty(old('employee_id')))
                <option value="{{ request()->get('e') }}" selected="selected">{{ old('$employeeSearch->first_name') }}</option>
            @elseif(isset($employeeSearch) && !empty($employeeSearch->id))
                <option value="{{ $employeeSearch->id }}" selected="selected">{{ $employeeSearch->first_name }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="date">Date: </label>
        <input class="form-control p-2"  type="date" name="punch_date" id="punch_date" onchange="search()" value="{{ request()->get('d') ?? request()->get('d') }}" >
    </div> 
    <div class="col-md-4">
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288;float:right;">Reset</button>
    </div>
</div>

<br>
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr style="background-color:#8bb8e0;">
            <th colspan="3" ></th>
            <th colspan="3" style="background-color:#09700f;" >Punch In</th>
            <th colspan="2" style="background-color:#d90707;">Punch Out</th>
            <th colspan="1" ></th>
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
        @forelse($employees as $employee)
            @forelse($employee->attendances as $attendance)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}</td>
                <td>{{ $employee->manager ? $employee->manager->first_name.' '.substr($employee->manager->middle_name,0,1).' '.$employee->manager->last_name:'--' }}</td>
                <td>{{ $attendance->punch_in_ip}}</td>
                <td>{{ $attendance->punch_in_time}}</td>
                <td>{{ $attendance->reason }}</td>
                <td>{{ $attendance->punch_out_ip }}</td>
                <td>{{ $attendance->punch_out_time }}</td>
                @if(!$attendance->punch_out_time)
                    <td style="background-color:yellow">N/A</td>   
                @else
                    <td>{{round((strtotime($attendance->punch_out_time) - strtotime($attendance->punch_in_time))/60/60)}} </td>
                @endif 
            </tr>
            @empty
                @if(!request()->get('e'))
                    <tr>
                            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                            <td>{{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}</td>
                            <td>{{ $employee->manager ? $employee->manager->first_name.' '.substr($employee->manager->middle_name,0,1).' '.$employee->manager->last_name : '--'}}</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                        </tr>
                @endif
            @endforelse
            
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
        let employee_id = $('#employee_id').val();
        console.log(employee_id);
        if(date)
            $(location).attr('href','/punch-in-detail?d='+date);
        if(employee_id)
            $(location).attr('href','/punch-in-detail?e='+employee_id);

    }

    function reset(){
        $(location).attr('href','/punch-in-detail');
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