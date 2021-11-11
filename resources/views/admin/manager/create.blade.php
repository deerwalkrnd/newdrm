@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Manager</h3>
    <form method="POST" action="/manager">
        @csrf
        @include('admin.manager._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection