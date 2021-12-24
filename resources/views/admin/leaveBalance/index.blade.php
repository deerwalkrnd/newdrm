@extends('layouts.hr.app')

@section('title','Leave Report')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Leave Balance","url"=>"/"])
<table class="unit_table mx-auto">
    <thead></thead>
        <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
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


@include('layouts.basic.tableFoot')
@endsection