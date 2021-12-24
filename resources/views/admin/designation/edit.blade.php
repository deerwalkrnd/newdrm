@extends('layouts.hr.app')

@section('title','Designation')

@section('content')
<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Edit Designation</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
        <div class="col-md-0 col-sm-0">
            <form class="main_form p-4" method="POST" action="/designation/{{$designation->id}}">
                @csrf
                @method('PUT')
                @include('admin.designation._form')
                <center><button type="submit" class="btn btn-primary mt-2">Update</button></center>   
            </form>
        </div>
    </div>
</section>
<!-- form end -->
@endsection