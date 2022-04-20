@extends('layouts.hr.app')

@section('title','Terminated Employee')

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    <strong>{{ $message }}</strong>
</div>
@endif

@section('content')
<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
        <div class="col-md-12 col-sm-8" style="background-color:aliceblue; padding: 20px 40px;">
            <form class="main_form p-4" method="POST" action="/employee/terminate" onsubmit="return verify()" enctype='multipart/form-data'>
                <legend>
                    <center>Terminate Employee</center>
                </legend>    
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label class="form-label" for="employee_id">Employee</label>
                            <select class="employee-livesearch form-control p-3" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --">
                            </select>
                            @error('employee_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- manager_id -->
                <center><button type="submit" class="btn btn-primary mt-2">Terminate</button></center>
            </form>
        </div>
    </div>
</section>
<!-- form end -->


@include('layouts.basic.tableHead',["table_title" => "Terminated Employee List"])

<div class="row m-5 d-flex aligns-items-center justify-content-center ">
    <div class="col-md-4">
        <!-- <label class="form-label" for="unit_id">Unit: </label> -->
        <select class="form-control p-2" name="unit_id" onchange="search()" id="unit_id" data-placeholder="-- Choosee Unit --">
            <option value="0"  selected disabled class="text-center">----------   Choose Unit   -----------</option>
            @foreach($units as $unit)
                <option value="{{$unit->id}}"
                    {{ (!empty(old('unit_id')) && old('unit_id') == $unit->id) ? 'selected': ''}}
                    {{ ((request()->get('u')) && (request()->get('u')) == $unit->id && empty(old('unit_id'))) ? 'selected' : ''}}>{{ $unit->unit_name }}
                </option>
            @endforeach
        </select>
    </div> 
    <!-- <div class="col-md-7 mt-1"> -->
        <div class="col-md-1 ml-3">
            <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288;float:right;width:100px;">Reset</button>
        </div>
        <div class="col-md-1">
            <a href="{{ '/download/employee/terminate?'.request()->getQueryString() }}"  id="export" class="btn btn-success border-0 text-white" style="float:right;width:100px;">Export</a>
        </div>
    <!-- </div> -->
</div>

<table class="unit_table mx-auto drmDataTable">
    <thead>
    <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Name</th>
        <th scope="col">Title</th>
        <th scope="col">Manager</th>
        <th scope="col">Join Date</th>
        <th scope="col">Terminated Date</th>
        <th scope="col">Status</th>
    </tr>
    </thead>
    <tbody>
    @forelse($terminatedEmployees as $employee)
    <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td><a href="/employee/profile/{{$employee->id}}">{{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}</a></td>
        <td>{{ $employee->designation->job_title_name }}</td>
        <td>{{ isset($employee->manager) ? $employee->manager->first_name.' '.substr($employee->manager->middle_name,0,1).' '.$employee->manager->last_name : "N/A"}} </td>
        <td>{{ $employee->join_date }}</td>
        <td>{{ $employee->terminated_date }}</td>
        <td class="text-center">
            <button type="button" class="btn btn-danger">Terminated</button>
        </td>
    </tr>
    @empty
    <tr>
        <th colspan=11 class="text-center text-dark">No Employee Terminated</th>
    </tr>
    @endforelse
    </tbody>
</table>
{{-- $employees->links() --}}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })

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

    function verify(){
        let employee = document.getElementById('employee_id').innerText;
        return (confirm('Do you want to terminate?') && prompt('Enter Employee\'s Name to be Terminated') === employee);
    }

    function reset(){
        $(location).attr('href','/employee/terminate');
    }

    //Search Unit wise
    function search(){
        console.log('here');
        let unit_id = $("#unit_id").val();
        console.log(unit_id);
        if(unit_id)
            $(location).attr('href','/employee/terminate?u='+unit_id);
    };
</script>
@endsection