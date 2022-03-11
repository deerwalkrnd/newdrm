@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/shift/create"><button class="btn btn-primary float-right">Add Shift</button></a>
    <h3 class="text-success text-center">Shift List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Shift Type</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($shifts as $shift)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $shift->name }}</td>
                    <td>
                        <a href="/shift/edit/{{ $shift->id }}">Edit</a> 
                        |
                        <form action="/shift/{{ $shift->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Shift Type Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection