@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Leave Type</h3>
    <form method="POST" action="/leaveType/{{$leaveType->id}}">
        @csrf
        @method('PUT')
        @include('admin.leaveType._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection