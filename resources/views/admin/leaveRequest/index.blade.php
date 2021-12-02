@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/leave-request/create"><button class="btn btn-primary float-right">Leave Request</button></a>
    <h3 class="text-success text-center">Leave Request List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Employee</th>
                    <th scope="col">Leave Type</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Days</th>
                    <th scope="col">Reason</th>
                    <th scope="col">State</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leaveRequests as $leaveRequest)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $leaveRequest->employee->first_name.' '.$leaveRequest->employee->last_name }}</td>
                    <td>{{ $leaveRequest->leaveType->name }}</td>
                    <td>{{ $leaveRequest->start_date }}</td>
                    <td>{{ $leaveRequest->end_date }}</td>
                    <td>{{ $leaveRequest->days }}</td>
                    <td>{{ $leaveRequest->reason }}</td>
                    <td>{{ $leaveRequest->acceptance }}</td>
                    <td>
                        @if($leaveRequest->acceptance == 'pending')
                        <form action="/leave-request/{{ $leaveRequest->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                        |
                        <form action="/leave-request/accept/{{ $leaveRequest->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="delete">Accept</button>
                        </form>
                        |
                        <form action="/leave-request/reject/{{ $leaveRequest->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="delete">Reject</button>
                        </form>
                        @else
                        <div class="dropdown">
                            <button class="btn btn-{{ $leaveRequest->acceptance == 'accepted' ? 'success' : 'danger' }} dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-expanded="false">
                            {{ $leaveRequest->acceptance }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <form action="/leave-request/accept/{{ $leaveRequest->id }}" method="POST" class="dropdown-item">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="delete" style="width:100%;">Accept</button>
                                </form>
                                <!-- accept -->
                                <form action="/leave-request/reject/{{ $leaveRequest->id }}" method="POST" class="dropdown-item">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="delete" style="width:100%;">Reject</button>
                                </form>
                                <!-- reject -->
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No LeaveRequest Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $leaveRequests->links() }}
    </div>
</div>
@endsection