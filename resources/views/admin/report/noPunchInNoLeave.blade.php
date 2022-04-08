@extends('layouts.hr.app')

@section('title','No Punch In No Leave Report')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "No Punch In No Leave Report"])

<div class="row">
    <div class="col-md-4">
        <label class="form-label" for="employee_id">Employee:</label>
        <select class="employee-livesearch form-control p-3" onchange="test()"  name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --">
            @if(!empty(old('employee_id')))
                <option value="{{ request()->get('e') }}" selected="selected">{{ old('$employeeSearch->first_name') }}</option>
            @elseif(isset($employeeSearch) && !empty($employeeSearch->id))
                <option value="{{ $employeeSearch->id }}" selected="selected">{{ $employeeSearch->first_name.' '.$employeeSearch->last_name }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="date">Date: </label>
        <input class="form-control p-2"  type="date" name="date" id="date" onchange="test()" value="{{ request()->get('d') ?? request()->get('d') }}" >
    </div> 
    <div class="col-md-4">
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288;float:right;">Reset</button>
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
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $record)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{ $record->employee->first_name.' '.substr($record->employee->middle_name,0,1).' '.$record->employee->last_name }}</td>
                @if($record->employee->manager != NULL)
                    <td>{{ $record->employee->manager->first_name.' '.substr($record->employee->manager->middle_name,0,1).' '.$record->employee->manager->last_name }}</td>
                @else
                    <td> -- </td>
                @endif
                <td>{{ $record->date }}</td>
                <td>
                    <form action="/force-punch-in/{{ $record->id }}" onsubmit="return confirm('Are you sure?')" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="code" value="OXqSTexF5zn4uXSp">
                        <input type="hidden" name="employee_id" value="{{$record->employee_id}}">
                        <button type="submit"  class="border-0 btn btn-primary">Force Punch In</button>
                    </form>
                </td>
            </tr>          
        @empty
        <tr>
            <th colspan=11 class="text-center text-dark">No Records of No Punch In No Leave Request</th>
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

    //Search leave by date and employee_id
    function test(){
        let date = $('#date').val();
        let employee_id = $('#employee_id').val();
        if(date)
            $(location).attr('href','/no-punch-in-leave?d='+date);
        if(employee_id)
            $(location).attr('href','/no-punch-in-leave?e='+employee_id);
    }

    function reset(){
        $(location).attr('href','/no-punch-in-leave');
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