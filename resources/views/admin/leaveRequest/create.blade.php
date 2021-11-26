@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Leave Request</h3>
    <form method="POST" action="/leave-request">
        @csrf
        @include('admin.leaveRequest._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection