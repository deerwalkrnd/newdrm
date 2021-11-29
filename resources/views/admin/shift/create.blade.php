@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Shift</h3>
    <form method="POST" action="/shift">
        @csrf
        @include('admin.shift._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection