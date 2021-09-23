@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Organization</h3>
    <form method="POST" action="/organization/{{$organization->id}}">
        @csrf
        @method('PUT')
        @include('admin.organization._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection