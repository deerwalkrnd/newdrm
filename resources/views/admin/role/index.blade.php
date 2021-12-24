@extends('layouts.hr.app')

@section('title','Role')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Role List", "url" => "/role/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
       <th scope="col" class="ps-4">S.N</th>
       <th scope="col">Role Name</th>
        <th scope="col">Action</th>
                
    </tr>
   @forelse($roles as $role)
    <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td>{{ $role->authority }}</td>
        <td class="text-center">
            <a href="/role/edit/{{ $role->id }}"><i class="far fa-edit"></i></a> 
            | 
            <form action="/role/{{ $role->id }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" name="delete" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
            </form>
            
        </td>
        
    </tr>
    @empty
    <tr>
        <th colspan=5 class="text-center text-dark">No Role Found</th>
    </tr>
    @endforelse
</table>
{{ $roles->links() }}

@include('layouts.basic.tableFoot')
@endsection