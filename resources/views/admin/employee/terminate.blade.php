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
            <form class="main_form p-4" method="POST" action="/employee/terminate" onsubmit="return confirm('Do you want to terminate?');" enctype='multipart/form-data'>
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
<table class="unit_table mx-auto drmDataTable">
    <thead>
    <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Name</th>
        <th scope="col">Title</th>
        <th scope="col">Manager</th>
        <th scope="col">Join Date</th>
        <th scope="col">Status</th>
    </tr>
    </thead>
    <tbody>
    @forelse($terminatedEmployees as $employee)
    <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td><a href="/employee/profile/{{$employee->id}}">{{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}</a></td>
        <td>{{ $employee->designation->job_title_name }}</td>
        <td>{{ $employee->manager != NULL ? $employee->manager->first_name.' '.substr($employee->manager->middle_name,0,1).' '.$employee->manager->last_name : "N/A"}} </td>
        <td>{{ $employee->join_date }}</td>
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
</script>
@endsection