@extends('layouts.hr.app')

@section('title','Designation')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Designation List", "url" => "/designation/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="pl-4">S.N</th>
            <th scope="col">Job Title</th>
            <th scope="col">Job Description</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
      @forelse($designations as $designation)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $designation->job_title_name }}</td>
            <td>{{ $designation->job_description }}</td>
            <td class="text-center">
                <a href="/designation/edit/{{ $designation->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/designation/{{ $designation->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="border-0" type="submit" class="delete"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Designation Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $designations->links() --}}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection