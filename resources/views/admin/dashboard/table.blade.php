@extends('layouts.hr.app')

@section('title','Table')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Test", "url" => "/organization/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
        <th>Unit</th>
        <th>Organization</th>
        <th>Organization Code</th>
        <th class="text-center">Action</th>
    </tr>
    <tr>
        <td>Academics</td>
        <td>DWIT College</td>
        <td>09</td>
        <td class="text-center action"><i class="far fa-edit"></i></td>
    </tr>
</table>
{{-- $leaveTypes->links() --}}
@include('layouts.basic.tableFoot')
@endsection