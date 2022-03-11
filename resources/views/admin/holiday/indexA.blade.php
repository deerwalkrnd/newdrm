@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/holiday/create"><button class="btn btn-primary float-right">Add Holiday</button></a>
    <h3 class="text-success text-center">Holiday List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Name</th>
                    <th scope='col'>Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($holidays as $holiday)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $holiday->name }}</td>
                    <td>{{ $holiday->date }}</td>
                    <td>
                        <a href="/holiday/edit/{{ $holiday->id }}">Edit</a> 
                        | 
                        <form action="/holiday/{{ $holiday->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Holiday Created</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $holidays->links() }}
    </div>
</div>
@endsection