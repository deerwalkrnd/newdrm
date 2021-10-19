@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Unit</h3>
    <form method="POST" action="/unit">
        @csrf
        @include('admin.unit._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection