@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Manager</h3>
    <form method="POST" action="/manager/{{$manager->id}}">
        @csrf
        @method('PUT')
        @include('admin.manager._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection