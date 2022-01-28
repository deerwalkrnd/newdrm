@extends('layouts.hr.app')

@section('title','Profile')

@section('content')


<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">My Profile</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<!-- form start -->
<section class="form_container mx-auto">
    <div class="row mx-auto">
        <div class="col-md-2 col-sm-4 mb-4 mx-auto">
            <img src="/assets/images/image.png" class="img-thumbnail img-fluid" width="100%">
        </div>

        <div class="col-md-10 col-sm-8" style="background-color:aliceblue; padding: 20px 40px;">
            <form class="main_form p-4" enctype='multipart/form-data'>  
                @include('admin.employee.profile_form')
            </form>
        </div>
    </div>
</section>
<!-- form end -->
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
        var start_time = $("#start_time").val();
        var end_time = $('#end_time').val();
        if(start_time && end_time)
            $('#shift_time').show();
    })
    
</script>
@endsection
