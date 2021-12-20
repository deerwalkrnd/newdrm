@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <!-- <button class="btn btn-primary float-right">Leave Request</button> -->
    <h3 class="text-success text-center">Leave balance</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">Leave Type</th>
                    <th scope="col">Accrued</th>
                    <th scope="col">Allowed</th>
                    <th scope="col">Leave Taken</th>
                    <th scope="col">Balance</th>
                </tr>
            </thead>

            <tbody>
                @forelse($lists as $leaveName => $data)
                <tr>
                    <td>{{ $leaveName }}</td>
                    <td>{{ $data['accrued'] }}</td>
                    <td>{{ $data['allowed'] }}</td>
                    <td>{{ $data['taken'] }}</td>
                    <td>{{ $data['balance'] }}</td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Leave Balance Report to Show</th>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection