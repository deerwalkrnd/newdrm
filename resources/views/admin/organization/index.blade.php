@extends('layouts.hr.app')

@section('title','Organization')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Organization List", "url" => "/organization/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
       <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Organization Name</th>
        <th scope="col">Code</th>
        <th scope="col">Action</th>
                
    </tr>
    @forelse($organizations as $organization)
    <tr>
        <th scope="row" class="pl-4 text-dark">{{ $loop->iteration }}</th>
        <td>{{ $organization->name }}</td>
        <td>{{ $organization->code }}</td>
        <td class="text-center">
            <a href="/organization/edit/{{ $organization->id }}"><i class="far fa-edit"></i></a> 
            | 
            <form action="/organization/{{ $organization->id }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" name="delete" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
            </form>
            
        </td>
        
    </tr>
    @empty
    <tr>
        <th colspan=5 class="text-center text-dark">No Organization Found</th>
    </tr>
    @endforelse
</table>
{{ $organizations->links() }}

@include('layouts.basic.tableFoot')
@endsection