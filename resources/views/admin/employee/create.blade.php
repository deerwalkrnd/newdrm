@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Employee</h3>
    <form method="POST" action="/employee">
        @csrf
        @include('admin.employee._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection