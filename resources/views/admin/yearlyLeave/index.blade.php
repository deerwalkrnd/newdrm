@extends('layouts.hr.app')

@section('title','Yearly Leave')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Yearly Leave List", "url" => "/yearly-leaves/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
        <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Leave Type</th>
        <th scope="col">Organization</th>
        <th scope="col">Days</th>
        <th scope="col">Status</th>
        <th scope="col">Leave Year</th>
        <th scope="col">Action</th>
    </tr>
   @forelse($yearlyLeaves as $yearlyLeave)
    <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td>{{ $yearlyLeave->leaveType->name}}</td>
        <td>{{ $yearlyLeave->organization->name }}</td>
        <td>{{ $yearlyLeave->days }}</td>
        <td>{{ $yearlyLeave->status }}</td>
        <td>{{ $yearlyLeave->year }}</td>
        <td class="text-center">
            <a href="/yearly-leaves/edit/{{ $yearlyLeave->id }}"><i class="far fa-edit"></i></a> 
            |
            <form action="/yearly-leaves/{{ $yearlyLeave->id }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete border-0 action"><i class="fas fa-trash-alt action"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <th colspan=5 class="text-center text-dark">No Yearly Leave Found</th>
    </tr>
    @endforelse
</table>
{{ $yearlyLeaves->links() }}


@include('layouts.basic.tableFoot')
@endsection