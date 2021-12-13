@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Leave Request</h3>
    <form method="POST" action="/leave-request/subordinate-leave">
        @csrf
        <div class="form-group">
            <label for="employee_id">Employee Name</label>
            <select class="livesearch form-control p-3" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --"></select>
            @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <!-- choose employee -->
        @include('admin.leaveRequest._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection