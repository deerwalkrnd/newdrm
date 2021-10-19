@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Employee</h3>
    <form method="POST" action="/employee/{{$employee->id}}">
        @csrf
        @method('PUT')
        @include('admin.employee._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection