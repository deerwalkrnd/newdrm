@extends('layouts.hr.app')

@section('title','File Category')

@section('content')
<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Edit File Category</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
        <!-- <div class="col-md-0 col-sm-0"> -->
            <form method="POST" class="main_form p-4" action="/file-category/{{$fileCategory->id}}" enctype='multipart/form-data'>
                @csrf
                @method('PUT')
                @include('admin.fileCategory._form')
                <center><button type="submit" class="btn btn-primary mt-2">Update</button></center>   
            </form>
        <!-- </div> -->
    </div>
</section>
<!-- form end -->
@endsection