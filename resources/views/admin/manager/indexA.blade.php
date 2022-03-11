@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/manager/create"><button class="btn btn-primary float-right">Add Manager</button></a>
    <h3 class="text-success text-center">Manager List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Manager Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($managers as $manager)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ ucfirst($manager->employee->first_name).' '.ucfirst($manager->employee->last_name) }}</td>
                    <td>{{ ucfirst($manager->is_active) }}</td>
                    <td>
                        <a href="/manager/edit/{{ $manager->id }}">Edit</a> 
                        | 
                        <form action="/manager/{{ $manager->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Unit Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $managers->links() }}
    </div>
</div>
@endsection