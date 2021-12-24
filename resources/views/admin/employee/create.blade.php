@extends('layouts.hr.app')

@section('title','Form')

@section('content')


<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Create Employee</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
        <div class="col-md-2 col-sm-4 mb-4 mx-auto"style="margin-bottom: 58% !important;">
            <img src="/assets/images/image.png" class="img-thumbnail img-fluid" width="100%">
        </div>

        <div class="col-md-10 col-sm-8">
            <form class="main_form p-4" method="POST" action="/employee" enctype='multipart/form-data'>
                <legend>
                    <center>Personal Details</center>
                </legend>    
                @csrf
                @include('admin.employee._form')
                <center><button type="submit" class="btn btn-primary mt-2">Add</button></center>
            </form>
        </div>
    </div>
</section>
<!-- form end -->
@endsection