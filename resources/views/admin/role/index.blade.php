@extends('layouts.admin.app')
@section('content')
<div class="my-table">
    <a href="/role/create"><button class="btn btn-primary float-right">Add Role</button></a>
    <h3 class="text-success text-center">Role List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Role Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($roles as $role)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $role->authority }}</td>
                    <td>
                        <a href="/role/edit/{{ $role->id }}">Edit</a> 
                        | 
                        <form action="/role/{{ $role->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" name="delete" class="delete">Delete</button>
                        </form>
                        
                    </td>
                    
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Role Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $roles->links() }}
    </div>
</div>
@endsection