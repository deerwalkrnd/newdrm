@extends('layouts.admin.app')
@section('content')
<div class="my-table">
    <a href="/organization/create"><button class="btn btn-primary float-right">Add Organization</button></a>
    <h3 class="text-success text-center">Organization List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Organization Name</th>
                    <th scope="col">Code</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($organizations as $organization)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $organization->name }}</td>
                    <td>{{ $organization->code }}</td>
                    <td>
                        <a href="/organization/edit/{{ $organization->id }}">Edit</a> 
                        | 
                        <form action="/organization/{{ $organization->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" name="delete" class="delete">Delete</button>
                        </form>
                        
                    </td>
                    
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Organization Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $organizations->links() }}
    </div>
</div>
@endsection