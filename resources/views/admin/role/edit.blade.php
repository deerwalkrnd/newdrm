@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Role</h3>
    <form method="POST" action="/role/{{$role->id}}">
        @csrf
        @method('PUT')
        @include('admin.role._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection