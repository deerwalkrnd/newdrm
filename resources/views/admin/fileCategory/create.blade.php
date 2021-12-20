@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add File Category</h3>
    <form method="POST" action="/file-category" enctype='multipart/form-data'>
        @csrf
        @include('admin.fileCategory._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection