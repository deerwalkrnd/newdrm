@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Role</h3>
    <form method="POST" action="/role">
        @csrf
        @include('admin.role._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection