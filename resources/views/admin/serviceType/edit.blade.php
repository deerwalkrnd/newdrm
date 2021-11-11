@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Service Type</h3>
    <form method="POST" action="/serviceType/{{$serviceType->id}}">
        @csrf
        @method('PUT')
        @include('admin.serviceType._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection