@extends('layouts.hr.app')

@section('title','Leave Report')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Leave Balance Report"])


<div class="row">
    <div class="col-md-3">
        <label class="form-label" for="employee_id">Employee:</label>
        <select class="employee-livesearch form-control p-3" onchange="search()"  name="employee_id" id="employee_id" data-placeholder="-- Choosee Employee --">
            @if(!empty(old('employee_id')))
                <option value="{{ request()->get('e') }}" selected="selected">{{ old("$employeeSearch->first_name.' '.$employeeSearch->middle_name.' '.$employeeSearch->last_name") }}</option>
            @elseif(isset($employeeSearch) && !empty($employeeSearch->id))
                <option value="{{ $employeeSearch->id }}" selected="selected">{{ $employeeSearch->first_name.' '.$employeeSearch->middle_name.' '.$employeeSearch->last_name }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="year">Year: </label>
        <select class="form-control p-2" name="year" onchange="search()" id="year">
            <option value="0" disabled selected>-- Choose Year --</option>
            <option value="1" selected>Since Joined Year</option>
            @for($i=2067; $i<= $thisYear; $i++)
                <option value="{{$i}}" {{ (request()->get('d')) ? (request()->get('d')==$i ? 'selected':'') :($i == $thisYear ? 'selected':'') }}>{{ $i }}</option>
            @endfor
        </select>
    </div> 
    <div class="col-md-3">
        <label class="form-label" for="unit_id">Unit: </label>
        <select class="form-control p-2" name="unit_id" onchange="search()" id="unit_id" data-placeholder="-- Choosee Employee --">
            <option value="0"  selected disabled>-- Choose Unit --</option>
            @foreach($units as $unit)
                <option value="{{$unit->id}}"
                    {{ (!empty(old('unit_id')) && old('unit_id') == $unit->id) ? 'selected': ''}}
                    {{ ((request()->get('u')) && (request()->get('u')) == $unit->id && empty(old('unit_id'))) ? 'selected' : ''}}>{{ $unit->unit_name }}
                </option>
            @endforeach
        </select>
    </div> 
    
    <div class="col-md-3 row">
        <div class="mb-1 mt-1">
            <a href="{{ '/download/leave-balance-report?'.request()->getQueryString() }}"  id="export" class="btn btn-success border-0 text-white" style="float:right;width:100px;">Export</a>
        </div>
        <div>
            <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288;float:right;width:100px;">Reset</button>
        </div>
    </div>
    <!-- <p>{{ '/download/leave-balance-report?'.request()->getQueryString() }}</p>
    <a href="{{ '/download/leave-balance-report?'.request()->getQueryString() }}" class="btn btn-success btn-sm" >Export</a> -->
</div>
<br>
 <div class="col-md-4">
    <em>Note: AC-Accrued, A-Allowed, T-Taken, B-Balance</em>
</div>
<br>
<div class="table-responsive">
<table class="unit_table mx-auto">
    <thead></thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4" rowspan=2>S.N</th>
            <th scope="col" rowspan=2>Employee</th>
            <th scope="col" rowspan=2>Year</th>
            <th scope="col" rowspan=2>Unit</th>
            @foreach($leaveTypes as $leaveType)
            <th scope="col" colspan=4>{{ $leaveType->name }}</th>
            @endforeach
            <th scope="col" colspan=1>Unpaid</th>
            <th scope="col" colspan=2>Carry Over</th>
            <th scope="col" colspan=2>Exceeded</th>
        </tr>
        <tr class="table_title" style="background-color: #0f5288;">
            @for($i = $leaveTypesCount; $i>0; $i--)
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            @endfor
            <th>Total Taken</th>
            <th>A</th>
            <th>T</th>
            <th>Total Leave Days</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{$record['name']}}</td>
                <td>{{$record['leaves']['year']}}</td>
                <td>{{$record['unit']}}</td>
                <!-- Personal/Home -->
                @forelse($leaveTypes as $leaveType)
                    @if(array_key_exists($leaveType->name,$record['leaves']))
                        @if($record['leaves'][$leaveType->name]['balance'] < 0)
                            <td class="bg-danger">{{$record['leaves'][$leaveType->name]['accrued']}}</td>
                            <td class="bg-danger">{{$record['leaves'][$leaveType->name]['allowed']}}</td>
                            <td class="bg-danger">{{$record['leaves'][$leaveType->name]['taken']}}</td>
                            <td class="bg-danger">{{$record['leaves'][$leaveType->name]['balance']}}</td>
                        @else
                            <td>{{$record['leaves'][$leaveType->name]['accrued']}}</td>
                            <td>{{$record['leaves'][$leaveType->name]['allowed']}}</td>
                            <td>{{$record['leaves'][$leaveType->name]['taken']}}</td>
                            <td>{{$record['leaves'][$leaveType->name]['balance']}}</td>
                        @endif
                    @else
                        <td>0.0</td>
                        <td>0.0</td>
                        <td>0.0</td>
                        <td>0.0</td>
                    @endif
                @empty
                <td>0.0</td>
                <td>0.0</td>
                <td>0.0</td>
                <td>0.0</td>
                @endforelse
                <!-- Unpaid -->
                <td>{{$record['total_unpaid_leaves']}}</td>
                <!-- CarryOver -->
                @if(array_key_exists('Carry Over',$record['leaves']))
                    <td>{{$record['leaves']['Carry Over']['allowed']}}</td>
                    <td>{{$record['leaves']['Carry Over']['taken']}}</td>
                @else
                    <td>0.0</td>
                    <td>0.0</td>
                @endif
                <!-- Sum of Negative Leaves -->
                <td>{{$record['exceeded_leave_days']}}</td>

            </tr>
        @endforeach
    </tbody>    
</table>
</div>
{{ $employees->links() }}

@include('layouts.basic.tableFoot')
@endsection



@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })

    function reset(){
        $(location).attr('href','/leave-balance-report');
    }

    function exportTasks(_this) {
      let _url = $(_this).data('href');
      window.location.href = _url;
    }

    // function download(){
    //     let date = $('#year').val();
    //     let employee_id = $('#employee_id').val();
    //     let download = 1;
    //     $(location).attr('href','/leave-balance-report?d='+date+'&e='+employee_id'&download='+download);      
    // }

    //Search by date or Employee
    function search(){
        console.log('here');
        let date = $('#year').val();
        let employee_id = $('#employee_id').val();
        let unit_id = $("#unit_id").val();
        // $("export").click(function{
        //     $(location).attr('href','/leave-balance-report?d='+date+'&e='+employee_id+'&u='+unit_id+'&download=1');
        // });
        // console.log(date,!employee_id,unit_id);

        if(unit_id && employee_id && date)
            $(location).attr('href','/leave-balance-report?u='+unit_id+'&d='+date+'&e='+employee_id);
        else if(unit_id && employee_id)
            $(location).attr('href','/leave-balance-report?u='+unit_id+'&e='+employee_id);
        else if(unit_id && date)
            $(location).attr('href','/leave-balance-report?u='+unit_id+'&d='+date);
        else if(employee_id && date)
            $(location).attr('href','/leave-balance-report?d='+date+'&e='+employee_id);
        else if(unit_id)
            $(location).attr('href','/leave-balance-report?u='+unit_id);
        else if(date)
            $(location).attr('href','/leave-balance-report?d='+date);
        else if(employee_id)
            $(location).attr('href','/leave-balance-report?e='+employee_id);

    };

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