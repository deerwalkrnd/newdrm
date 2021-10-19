@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Leave-Type</h3>
    <form method="POST" action="/leaveType">
        @csrf
        @include('admin.leaveType._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection