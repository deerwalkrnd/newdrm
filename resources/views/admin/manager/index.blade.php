@extends('layouts.hr.app')

@section('title','Manager')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Manager List", "url" => "/manager/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
       <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Manager Name</th>
        <th scope="col">Status</th>
        <th scope="col">Action</th>
                
    </tr>
    @forelse($managers as $manager)
    <tr>
        <th scope="row" class="pl-4 text-dark">{{ $loop->iteration }}</th>
        <td>{{ ucfirst($manager->employee->first_name).' '.ucfirst($manager->employee->last_name) }}</td>
        <td>{{ ucfirst($manager->is_active) }}</td>
        <td class="text-center">
            <a href="/manager/edit/{{ $manager->id }}"><i class="far fa-edit"></i></a> 
            | 
            <form action="/manager/{{ $manager->id }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete border-0 action"><i class="fas fa-trash-alt"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <th colspan=5 class="text-center text-dark">No Unit Found</th>
    </tr>
    @endforelse
</table>
{{ $managers->links() }}

@include('layouts.basic.tableFoot')
@endsection