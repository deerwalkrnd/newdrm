@extends('layouts.hr.app')

@section('title','Profile')

@section('content')


<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">Edit Employee Contact</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
        <div class="col-md-2 col-sm-4 mb-4 mx-auto">
            <img src="{{ ($employee->image_name != NULL) ? asset($employee->image_name) : '/assets/images/image.png' }}" class="img-thumbnail img-fluid" width="100%">
        </div>

        <div class="col-md-10 col-sm-8" style="background-color:aliceblue; padding: 20px 40px;">
            <form method="POST" action="/employee-contact/{{$employee->id}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.employee.contact_form')
                <center><button type="submit" class="btn btn-primary mt-2">Update</button></center>
            </form>
        </div>
        
    </div>
</section>
<!-- form end -->
@endsection
