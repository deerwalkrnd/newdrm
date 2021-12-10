@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Holiday</h3>
    <form method="POST" action="/holiday/{{$holiday->id}}" enctype='multipart/form-data'>
        @csrf
        @method('PUT')
        @include('admin.holiday._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection