@extends('layouts.hr.app')

@section('title','Leave Type')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Leave Type List", "url" => "/leaveType/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Type</th>
            <th scope="col">Payment Status</th>
            <th scope="col">Include Holiday Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($leaveTypes as $leaveType)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $leaveType->name }}</td>
            <td> {{ $leaveType->paid_unpaid == 1? 'Paid':'Unpaid'}} </td>
            <td>{{ $leaveType->include_holiday }}</td>
            <td class="text-center">
                <a href="/leaveType/edit/{{ $leaveType->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/leaveType/{{ $leaveType->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Leave Type Created</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $leaveTypes->links() --}}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection