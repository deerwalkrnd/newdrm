@extends('layouts.admin.app')

@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Add Yearly Leave</h3>
    <form method="POST" action="/yearly-leaves">
        @csrf
        @include('admin.yearlyLeave._form')
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
@endsection