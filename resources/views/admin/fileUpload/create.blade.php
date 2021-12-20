@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Upload File</h3>
    <form method="POST" action="/file-upload" enctype="multipart/form-data">
        @csrf
        @include('admin.fileUpload._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection