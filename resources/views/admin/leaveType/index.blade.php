@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/leaveType/create"><button class="btn btn-primary float-right">Add Leave Type</button></a>
    <h3 class="text-success text-center">Leave Type List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Type</th>
                    <th scope="col">Payment Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leaveTypes as $leaveType)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $leaveType->name }}</td>
                    <td> {{ $leaveType->paid_unpaid == 1? 'Paid':'Unpaid'}} </td>
                    <td>
                        <a href="/leaveType/edit/{{ $leaveType->id }}">Edit</a> 
                        | 
                        <form action="/leaveType/{{ $leaveType->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Leave Type Created</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $leaveTypes->links() }}
    </div>
</div>
@endsection