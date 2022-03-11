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
            <form class="main_form p-4">
                <legend>
                    <center>Personal Details</center>
                </legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">First Name*</label>
                            <input type="text" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Last Name*</label>
                            <input type="text" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Date of Birth*</label>
                            <input type="date" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Marital Status*</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Gender</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio">
                                <label class="form-check-label">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio">
                                <label class="form-check-label">Female</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio">
                                <label class="form-check-label">Others</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Father's Name</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Grandfather's Name</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Mobile*</label>
                            <input type="number" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Alternate Mobile</label>
                            <input type="number" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Home Phone</label>
                            <input type="number" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Photo</label>
                            <input type="file" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Personal Email*</label>
                            <input type="number" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Resume (PDF)</label>
                            <input type="file" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Country*</label>
                            <input type="number" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Nationality</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Profile</label>
                            <input type="number" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Blood Group</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <center><button type="submit" class="btn btn-primary mt-2">Submit</button></center>
            </form>
        </div>
    </div>
</section>
<!-- form end -->
@endsection