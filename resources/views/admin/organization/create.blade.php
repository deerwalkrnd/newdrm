@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Organization</h3>
    <form method="POST" action="/organization">
        @csrf
        @include('admin.organization._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection