@extends('layouts.hr.app')

@section('title','Department')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Department List", "url" => "/department/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="pl-4">S.N</th>
            <th scope="col">Department Name</th>
            <th scope="col">Unit</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
      @forelse($departments as $department)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $department->name }}</td>
             @if($department->unit)
                <td>{{ $department->unit->unit_name }}</td>
            @else
                <td>All</td>
            @endif
            <td class="text-center">
                <a href="/department/edit/{{ $department->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/department/{{ $department->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="border-0" type="submit" class="delete"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Department Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $departments->links() --}}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection