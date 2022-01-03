@extends('layouts.hr.app')

@section('title','Employee')

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    <strong>{{ $message }}</strong>
</div>
@endif

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Employee List", "url" => "/employee/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
    <tr class="table_title" style="background-color: #0f5288;">
       <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Name</th>
        <th scope="col">Title</th>
        <th scope="col">Manager</th>
        <th scope="col">Organization</th>
        <th scope="col">Unit</th>
        <th scope="col">Internship/Trainee Date</th>
        <th scope="col">Join Date</th>
        <th scope="col">Y Since Date</th>
        <th scope="col">Status</th>
        <th scope="col">Position</th>
        <th scope="col">Action</th>
    </tr>
    </thead>
    <tbody>
    @forelse($employees as $employee)
    <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td><a href="/employee/edit/{{$employee->id}}">{{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}</a></td>
        <td>Title</td>
        <td>{{ $employee->manager != NULL ? $employee->manager->first_name.' '.substr($employee->manager->middle_name,0,1).' '.$employee->manager->last_name : "N/A"}} </td>
        <td>{{ $employee->organization->name }}</td>
        <td>{{ $employee->unit->unit_name }}</td>
        <td>{{ $employee->intern_trainee_ship_date }}</td>
        <td>{{ $employee->join_date }}</td>
        <td>{{ date('Y',strtotime($employee->join_date))}}</td>
        <td>{{ $employee->serviceType->service_type_name }}</td>
        <td>{{ $employee->designation->job_title_name }}</td>
        <td class="text-center">
            <a href="/employee/edit/{{$employee->id}}"><i class="far fa-edit"></i></a> 
            | 
            <form action="/employee/{{ $employee->id }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button  type="submit" class="delete border-0 action"><i class="fas fa-trash-alt"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <th colspan=11 class="text-center text-dark">No Employee Created</th>
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
</script>
@endsection