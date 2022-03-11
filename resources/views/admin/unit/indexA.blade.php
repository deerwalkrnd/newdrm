@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/unit/create"><button class="btn btn-primary float-right">Add Unit</button></a>
    <h3 class="text-success text-center">Unit List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Unit Name</th>
                    <th scope="col">Organization</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($units as $unit)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $unit->unit_name }}</td>
                    <td>{{ $unit->organization->name }}</td>
                    <td>
                        <a href="/unit/edit/{{ $unit->id }}">Edit</a> 
                        | 
                        <form action="/unit/{{ $unit->id }}" method="POST" class="d-inline">
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
        {{ $units->links() }}
    </div>
</div>
@endsection