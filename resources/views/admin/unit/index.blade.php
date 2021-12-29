@extends('layouts.hr.app')

@section('title','Unit')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Unit List", "url" => "/unit/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Unit Name</th>
            <th scope="col">Organization</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    @forelse($units as $unit)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $unit->unit_name }}</td>
            <td>{{ $unit->organization->name }}</td>
            <td class="text-center">
                <a href="/unit/edit/{{ $unit->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/unit/{{ $unit->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Unit Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $units->links() --}}


@include('layouts.basic.tableFoot')
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection