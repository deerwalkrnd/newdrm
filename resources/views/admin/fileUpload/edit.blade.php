@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Uploaded File</h3>
    <form method="POST" action="/file-upload/{{$fileUpload->id}}">
        @csrf
        @method('PUT')
        @include('admin.fileUpload._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection