@extends('layouts.hr.app')

@section('title','Table')

@section('content')
<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Unit List</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<div class="table_container">
    <!-- add button start -->
    <div class="button_div">
        <button class="add_button"><i class="fas fa-plus"></i> Add</button>
    </div>
    <!-- add button end -->

    <!-- table start -->
    <div>
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

            <tr>
                <td>Accounts</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

            <tr>
                <td>Admin</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

            <tr>
                <td>Academics</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

            <tr>
                <td>Accounts</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

            <tr>
                <td>Admin</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>
            <tr>
                <td>Academics</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

            <tr>
                <td>Accounts</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

            <tr>
                <td>Admin</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

            <tr>
                <td>Admin</td>
                <td>DWIT College</td>
                <td>09</td>
                <td class="text-center action"><i class="far fa-edit"></i></td>
            </tr>

        </table>
    </div>
    <!-- table end -->
</div>
@endsection