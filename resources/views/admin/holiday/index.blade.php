@extends('layouts.hr.app')

@section('title','Holiday')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Holiday List", "url" => "holiday/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
       <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Name</th>
        <th scope='col'>Date</th>
        <th scope="col">Action</th>
    </tr>
    @forelse($holidays as $holiday)
    <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td>{{ $holiday->name }}</td>
        <td>{{ $holiday->date }}</td>
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

</table>
{{ $holidays->links() }}


@include('layouts.basic.tableFoot')
@endsection