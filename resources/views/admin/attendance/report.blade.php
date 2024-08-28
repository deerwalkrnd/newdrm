@extends('layouts.hr.app')

@section('title','Employee')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Attendance Report"])


<div class="row">
    <div class="col-md-4">
        <label class="form-label" for="employee_id">Employee:</label>
        <select class="employee-livesearch form-control p-3" onchange="search()"  name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --">
            @if(isset($employeeSearch))
                <option value="{{ request()->get('e') ?? request()->get('e') }}" 
                selected="selected">{{ $employeeSearch->first_name.' '.$employeeSearch->middle_name.' '.$employeeSearch->last_name }}</option>
            @endif   
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="date">Start Date: </label>
        <input class="form-control p-2"  type="date" name="start_date" id="start_date" onchange="search()" value="{{ request()->get('sd') ?? request()->get('sd') }}" >
    </div> 
    <div class="col-md-4">
        <label class="form-label" for="date">End Date: </label>
        <input class="form-control p-2"  type="date" name="end_date" id="end_date" onchange="search()" value="{{ request()->get('ed') ?? request()->get('ed') }}" >
    </div> 
    <div class="col-md-4">
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288;float:right;">Reset</button>
    </div>
</div>

<br>
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col">Name</th>
            @foreach($dates as $date)
                <th scope="col" data-dt-order="disable">{{ $date->format('Y-m-d') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($employees as $employee)
        <tr>
            <td>{{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}</td>
            
            @foreach($dates as $date)
                @php
                   $attendance = $employee->attendances->first(function ($att) use ($date) {
                        return \Carbon\Carbon::parse($att->punch_in_time)->isSameDay($date);
                    });
                    // dd($attendance);
                @endphp
                
                @if($attendance)
                    @php
                        $punchintime=\Carbon\Carbon::parse($attendance->punch_in_time)->format('H:i:s');
                        $punchouttime=\Carbon\Carbon::parse($attendance->punch_out_time)->format('H:i:s');
                        // dd($punchintime,$punchouttime);
                        // dd($attendance);
                    @endphp
                    @if ($punchintime > "13:30:00"||$punchouttime < "13:30:00")
                    <td>P/A</td>
                    @else
                    <td>P</td>
                    @endif
                @else
                    <td>A</td>
                @endif
            @endforeach
        </tr>
        @empty
        <tr>
            <td colspan="{{ count($dates) + 1 }}">No records found</td>
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

    // Search by date or Employee
    function search(){
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        let employee_id = $('#employee_id').val();
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
        alert('Start date should be less than or equal to end date.');
        return; 
    }
        // Create an array to hold query parameters
    let queryParams = [];

if (startDate) queryParams.push('sd=' + startDate);
if (endDate) queryParams.push('ed=' + endDate);
if (employee_id) queryParams.push('e=' + employee_id);

// Join query parameters with '&' and navigate to the constructed URL
if (queryParams.length > 0) {
    let queryString = queryParams.join('&');
    $(location).attr('href', '/attendancereport?' + queryString);
}
    }

    function reset(){
        $(location).attr('href','/attendancereport');
    }

    $('.employee-livesearch').select2({    
        ajax: {
            url: '/employee/search',
            data: function (params) {
                return {
                    q: params.term // The search query
                };
            },
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
               
                return {
                    results: $.map(data, function (item) {
                        let full_name = (item.middle_name === null) ? 
                                        item.first_name + " " + item.last_name : 
                                        item.first_name + " " + item.middle_name + " " + item.last_name;
                        return {
                            text: full_name,
                            id: item.id
                        };
                    })
                };
            },
            cache: true
        }
    });
</script>
@endsection
