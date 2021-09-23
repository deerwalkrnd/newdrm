@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Designation</h3>
    <form method="POST" action="/designation/{{$designation->id}}">
        @csrf
        @method('PUT')
        @include('admin.designation._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection