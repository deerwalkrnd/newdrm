@extends('layouts.hr.app')

@section('title','Leave Report')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Leave Balance"])


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
        <label class="form-label" for="year">Year: </label>
        <select class="form-control p-2" name="year" onchange="search()" id="year">
            @for($i=2011; $i<= date('Y'); $i++)
                <option value="{{$i}}" {{ (request()->get('d')) ? (request()->get('d')==$i ? 'selected':'') :($i == date('Y') ? 'selected':'') }}>{{ $i }}</option>
            @endfor
        </select>
    </div> 
    <!-- <div class="col-md-3"></div> -->
    <div class="col-md-4">
        <label for=""></label>
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288;float:right;">Reset</button>
    </div>
</div>
<br>

<table class="unit_table mx-auto">
    <thead></thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4" rowspan=2>S.N</th>
            <th scope="col" rowspan=2>Employee</th>
            <th scope="col" rowspan=2>Year</th>
            <th scope="col" colspan=4>Home</th>
            <th scope="col" colspan=4>Floating</th>
            <th scope="col" colspan=4>Sick</th>
            <th scope="col" colspan=4>Mourning</th>
            <th scope="col" colspan=4>Maternity</th>
            <th scope="col" colspan=4>Paternity</th>
            <th scope="col" rowspan=2>Unpaid</th>
            <th scope="col" rowspan=2>Carry Over</th>
        </tr>
        <tr class="table_title" style="background-color: #0f5288;">
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{$record['name']}}</td>
                <td>{{$record['leaves']['year']}}</td>
                <!-- Personal/Home -->
                <td>{{$record['leaves']['Personal']['accrued']}}</td>
                <td>{{$record['leaves']['Personal']['allowed']}}</td>
                <td>{{$record['leaves']['Personal']['taken']}}</td>
                <td>{{$record['leaves']['Personal']['balance']}}</td>
                <!-- Floating  -->
                <td>{{$record['leaves']['Floating']['accrued']}}</td>
                <td>{{$record['leaves']['Floating']['allowed']}}</td>
                <td>{{$record['leaves']['Floating']['taken']}}</td>
                <td>{{$record['leaves']['Floating']['balance']}}</td>
                <!-- Sick -->
                <td>{{$record['leaves']['Sick']['accrued']}}</td>
                <td>{{$record['leaves']['Sick']['allowed']}}</td>
                <td>{{$record['leaves']['Sick']['taken']}}</td>
                <td>{{$record['leaves']['Sick']['balance']}}</td>
                <!-- Mourning -->
                <td>{{$record['leaves']['Mourning']['accrued']}}</td>
                <td>{{$record['leaves']['Mourning']['allowed']}}</td>
                <td>{{$record['leaves']['Mourning']['taken']}}</td>
                <td>{{$record['leaves']['Mourning']['balance']}}</td>
                <!-- Maternity -->
                @if(array_key_exists('Maternity',$record['leaves']))
                <td>{{$record['leaves']['Maternity']['accrued']}}</td>
                <td>{{$record['leaves']['Maternity']['allowed']}}</td>
                <td>{{$record['leaves']['Maternity']['taken']}}</td>
                <td>{{$record['leaves']['Maternity']['balance']}}</td>
                @else
                <td>0.0</td>
                <td>0.0</td>
                <td>0.0</td>
                <td>0.0</td>
                @endif
                <!-- Paternity -->
                @if(array_key_exists('Paternity',$record['leaves']))
                <td>{{$record['leaves']['Paternity']['accrued']}}</td>
                <td>{{$record['leaves']['Paternity']['allowed']}}</td>
                <td>{{$record['leaves']['Paternity']['taken']}}</td>
                <td>{{$record['leaves']['Paternity']['balance']}}</td>
                 @else
                <td>0.0</td>
                <td>0.0</td>
                <td>0.0</td>
                <td>0.0</td>
                @endif
                <!-- Unpaid -->
                <td>0.0</td>
                <!-- CarryOver -->
                <td>{{$record['leaves']['Carry Over']['allowed']}}</td>

            </tr>
        @endforeach
    </tbody>    
</table>


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


    //Search by date or Employee
    function search(){
        let date = $('#year').val();
        let employee_id = $('#employee_id').val();
        if(date)
            $(location).attr('href','/leave-balance-report?d='+date);
        if(employee_id)
            $(location).attr('href','/leave-balance-report?e='+employee_id);

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