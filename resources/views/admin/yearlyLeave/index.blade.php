@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/yearly-leaves/create"><button class="btn btn-primary float-right">Add Yearly Leave</button></a>
    <h3 class="text-success text-center">Yearly Leave List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Leave Type</th>
                    <th scope="col">Organization</th>
                    <th scope="col">Days</th>
                    <th scope="col">Status</th>
                    <th scope="col">Leave Year</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($yearlyLeaves as $yearlyLeave)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $yearlyLeave->leaveType->name}}</td>
                    <td>{{ $yearlyLeave->organization->name }}</td>
                    <td>{{ $yearlyLeave->days }}</td>
                    <td>{{ $yearlyLeave->status }}</td>
                    <td>{{ $yearlyLeave->year }}</td>
                    <td>
                        <a href="/yearly-leaves/edit/{{ $yearlyLeave->id }}">Edit</a> 
                        |
                        <form action="/yearly-leaves/{{ $yearlyLeave->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Yearly Leave Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection