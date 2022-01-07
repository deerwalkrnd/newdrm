@extends('layouts.hr.app')

@section('title','Holiday')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Holiday List", "url" => "holiday/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Name</th>
            <th scope="col">Unit</th>
            <th scope='col'>Date</th>
            <th scope='col'>Female Only</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($holidays as $holiday)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $holiday->name }}</td>
            @if($holiday->unit)
                <td>{{ $holiday->unit->unit_name }}</td>
            @else
                <td>All</td>
            @endif
            <td>{{ $holiday->date }}</td>
            <td> @if($holiday->female_only) Yes @else No</td>@endif
            <td class="text-center">
                <a href="/holiday/edit/{{ $holiday->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/holiday/{{ $holiday->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"  class="delete border-0 action"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Holiday Created</th>
        </tr>
        @endforelse
    </tbody>

</table>
{{-- $holidays->links() --}}


@include('layouts.basic.tableFoot')
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection