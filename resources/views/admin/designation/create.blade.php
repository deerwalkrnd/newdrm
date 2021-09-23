@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Designation</h3>
    <form method="POST" action="/designation">
        @csrf
        @include('admin.designation._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection