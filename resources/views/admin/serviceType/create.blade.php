@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Service Type</h3>
    <form method="POST" action="/serviceType">
        @csrf
        @include('admin.serviceType._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection