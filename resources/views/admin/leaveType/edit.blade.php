@extends('layouts.hr.app')

@section('title','Leave Type')

@section('content')
<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Edit Leave Type</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
        <div class="col-md-0 col-sm-0">
            <form class="main_form p-4" method="POST" action="/leaveType/{{$leaveType->id}}">
                @csrf
                @method('PUT')
                @include('admin.leaveType._form')
                <center><button type="submit" class="btn btn-primary mt-2">Update</button></center>   
            </form>
        </div>
    </div>
</section>
<!-- form end -->
@endsection