@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Holiday</h3>
    <form method="POST" action="/holiday" enctype='multipart/form-data'>
        @csrf
        @include('admin.holiday._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection