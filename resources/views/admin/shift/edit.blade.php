@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Shift</h3>
    <form method="POST" action="/shift/{{$shift->id}}">
        @csrf
        @method('PUT')
        @include('admin.shift._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection