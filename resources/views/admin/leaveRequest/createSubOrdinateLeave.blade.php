@extends('layouts.hr.app')

@section('title','Sub-Ordinate Leave')

@section('content')
<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Create Sub-Ordinate Leave</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
    <form method="POST"class="main_form p-4" class="main_form p-4" action="/leave-request/subordinate-leave">
        @csrf
            <label for="employee_id">Employee Name</label>
            <select class="livesearch form-control p-3" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --"></select>
            @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        <!-- choose employee -->
        @include('admin.leaveRequest._form')
        <center><button type="submit" class="btn btn-primary mt-2">Add</button></center>   
        </form>
    </div>
</section>
<!-- form end -->
@endsection