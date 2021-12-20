@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit File Category</h3>
    <form method="POST" action="/file-category/{{$fileCategory->id}}" enctype='multipart/form-data'>
        @csrf
        @method('PUT')
        @include('admin.fileCategory._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection