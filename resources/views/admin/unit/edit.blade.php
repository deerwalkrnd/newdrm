@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Edit Unit</h3>
    <form method="POST" action="/unit/{{$unit->id}}">
        @csrf
        @method('PUT')
        @include('admin.unit._form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection