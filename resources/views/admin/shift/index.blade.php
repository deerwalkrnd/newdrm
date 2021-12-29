@extends('layouts.hr.app')

@section('title','Shift Type')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Shift List", "url" => "/shift/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Shift Type</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($shifts as $shift)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $shift->name }}</td>
            <td class="text-center">
                <a href="/shift/edit/{{ $shift->id }}"><i class="far fa-edit"></i></a> 
                |
                <form action="/shift/{{ $shift->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"  class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Shift Type Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $shifts->links() --}}

@include('layouts.basic.tableFoot')
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection