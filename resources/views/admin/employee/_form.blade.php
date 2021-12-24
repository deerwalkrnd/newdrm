@if($errors->any())
    <div class="alert alert-danger">
        <p><strong>Opps Something went wrong</strong></p>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif

<!-- Form Starts -->
<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="first_name">First Name*</label>
            <input type="text" class="form-control" id="first_name" placeholder="Enter Employee First Name" name="first_name" value="{{ !empty(old('first_name')) ? old('first_name') : $employee->first_name ?? '' }}">
            @error('first_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- firstname -->

    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="last_name">Last Name*</label>
            <input type="text" class="form-control" id="last_name" placeholder="Enter Employee Last Name" name="last_name" value="{{ !empty(old('last_name')) ? old('last_name') : $employee->last_name ?? '' }}">
            @error('last_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- lastname -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="middle_name">Middle Name</label>
            <input type="text" class="form-control" id="middle_name" placeholder="Enter Employee Middle Name" name="middle_name" value="{{ !empty(old('middle_name')) ? old('middle_name') : $employee->middle_name ?? '' }}">
            @error('middle_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- middlename -->

    <div class="col-md-6">
        <div class="mb-4">
            <label for="date_of_birth" class="form-label">DOB*</label>
            <input type="date" class="form-control" id="date_of_birth" placeholder="Enter Employee DOB" name="date_of_birth" value="{{ !empty(old('date_of_birth')) ? old('date_of_birth') : $employee->date_of_birth ?? '' }}">
            @error('date_of_birth')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        </div>
</div>

<!-- dob -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="marital_status">Marital Status*</label>
            <select class="form-control" id="marital_status" name="marital_status">
                <option value="" disabled="disabled" selected="selected">-- Choose Status --</option>
                <option 
                    value="Single" 
                    {{ (!empty(old('marital_status')) && old('marital_status') == 'Single') ? 'selected': ''}}
                    {{ (isset($employee) && $employee->marital_status == 'Single' && empty(old('marital_status'))) ? 'selected' : '' }} 
                    >
                    Single
                </option>
                <option 
                    value="Married" 
                    {{ (!empty(old('marital_status')) && old('marital_status') == 'Married') ? 'selected': ''}}
                    {{ (isset($employee) && $employee->marital_status == 'Married' && empty(old('marital_status'))) ? 'selected' : '' }} 
                    >
                    Married
                </option>
                <option 
                    value="Divorced" 
                    {{ (!empty(old('marital_status')) && old('marital_status') == 'Divorced') ? 'selected': ''}}
                    {{ (isset($employee) && $employee->marital_status == 'Divorced' && empty(old('marital_status'))) ? 'selected' : '' }} 
                    >
                    Divorced
                </option>
            </select>
            @error('marital_status')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

<!-- marital-status -->

    <div class="col-md-6">
        <div class="mb-4">
            <label for="gender">Gender*</label>
            <br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender0" value="Female" 
                {{ (isset($employee) && $employee->gender == 'Female') ? 'checked':''}}
                {{ old('gender') == 'Female' ? 'checked':'' }}>
                <label class="form-check-label" for="gender0">Female</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender1" value="Male" 
                {{ (isset($employee) && $employee->gender == 'Male') ? 'checked':''}}
                {{ old('gender') == 'Male' ? 'checked':''}}>
                <label class="form-check-label" for="gender1">Male</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender2" value="Other" 
                {{ (isset($employee) && $employee->gender == 'Other') ? 'checked':'' }}
                {{ old('gender') == 'Other' ? 'checked':'' }}>
                <label class="form-check-label" for="gender2">Other</label>
            </div>
            @error('gender')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- gender -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="father_name">Father Name</label>
            <input type="text" class="form-control" id="father_name" placeholder="Enter Employee Father Name" name="father_name" value="{{ !empty(old('father_name')) ? old('father_name') : $employee->father_name ?? '' }}">
            @error('father_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

<!-- father_name -->
    <div class="col-md-6">
        <div class="mb-4">
        <label for="mother_name">Mother Name</label>
        <input type="text" class="form-control" id="mother_name" placeholder="Enter Employee Mother Name" name="mother_name" value="{{ !empty(old('mother_name')) ? old('mother_name') : $employee->mother_name ?? '' }}">
        @error('mother_name')
            <p class="text-danger">{{ $message }}</p>
        @enderror
    </div>
</div>
<!-- mother_name -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="grand_father">Grandfather Name</label>
            <input type="text" class="form-control" id="grand_father" placeholder="Enter Employee grand_father" name="grand_father" value="{{ !empty(old('grand_father')) ? old('grand_father') : $employee->grand_father ?? '' }}">
            @error('grand_father')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- grand_father -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="mobile">Mobile*</label>
            <input type="text" class="form-control" id="mobile" placeholder="Enter Employee mobile" name="mobile" value="{{ !empty(old('mobile')) ? old('mobile') : $employee->mobile ?? '' }}">
            @error('mobile')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

<!-- mobile -->

    <div class="col-md-6">
        <div class="mb-4">
            <label for="alternative_mobile">Alternative Mobile</label>
            <input type="text" class="form-control" id="alternative_mobile" placeholder="Enter Employee alternative_mobile" name="alternative_mobile" value="{{ !empty(old('alternative_mobile')) ? old('alternative_mobile') : $employee->alternative_mobile ?? '' }}">
            @error('alternative_mobile')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- alternative_mobile -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="home_phone">Home Mobile</label>
            <input type="text" class="form-control" id="home_phone" placeholder="Enter Employee home_phone" name="home_phone" value="{{ !empty(old('home_phone')) ? old('home_phone') : $employee->home_phone ?? '' }}">
            @error('home_phone')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- home_phone -->

    <div class="col-md-6">
        <div class="mb-4">
            <label for="image">Image</label>
            <input type="file" class="form-control" id="image" name="image">
            @error('image')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- image -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="alter_email">Personal Email*</label>
            <input type="text" class="form-control" id="alter_email" placeholder="Enter Employee alter_email" name="alter_email" value="{{ !empty(old('alter_email')) ? old('alter_email') : $employee->alter_email ?? '' }}">
            @error('alter_email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

<!-- alter_email -->

    <div class="col-md-6">
        <div class="mb-4">
            <label for="cv">Resume (PDF)</label>
            <input type="file" class="form-control" id="cv" name="cv">
            @error('cv')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- cv -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="country">Country*</label>
            
            <select class="form-control" id="country" name="country">
                @if(isset($employee)) $country = $employee->country @endif
                @include('layouts.country_options')
            </select>
            @error('country')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

<!-- country -->

    <div class="col-md-6">
        <div class="mb-4">
            <label for="nationality">Nationality</label>
            <input type="text" class="form-control" id="nationality" placeholder="Enter Employee nationality" name="nationality" value="{{ !empty(old('nationality')) ? old('nationality') : $employee->nationality ?? '' }}">
            @error('nationality')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- nationality -->

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="profile">Profile</label>
            <textarea class="form-control" id="profile" placeholder="Enter Employee profile" name="profile">{{ !empty(old('profile')) ? old('profile') : $employee->profile ?? '' }}</textarea>
            @error('profile')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

<!-- profile -->

    <div class="col-md-6">
        <div class="mb-4">
            <label for="blood_group">Blood Group</label>
            <input type="text" class="form-control" id="blood_group" placeholder="Enter Employee blood group" name="blood_group" value="{{ !empty(old('blood_group')) ? old('blood_group') : $employee->blood_group ?? '' }}">
            @error('blood_group')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- blood_group -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="permanent_address">Province*</label>
            <select class="form-control" id="permanent_address" name="permanent_address">
                <option value="" disabled="disabled" selected="selected">-- Choose Province --</option>
                @foreach($provinces as $province)
                <option 
                    value="{{ $province->id }}" 
                    {{ (!empty(old('permanent_address')) && old('permanent_address') == $province->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->permanent_address == $province->id && empty(old('permanent_address'))) ? 'selected' : '' }} 
                    >{{$province->province_name}}</option>
                @endforeach
            </select>
            @error('permanent_address')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- province -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="permanent_district">Permanent District*</label>
            <select class="district-livesearch form-control p-3" name="permanent_district" id="permanent_district" data-placeholder="-- Choose District --">
            <option value="" selected disabled>--Select District--</option>
            @foreach($districts as $district)
            <option value="{{ $district->id }}" 
                {{ (!empty(old('permanent_district')) && old('permanent_district') == $district->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->permanent_district == $district->id && empty(old('permanent_district'))) ? 'selected' : '' }} 
                    >{{$district->district_name}}
            </option>    
            @endforeach
            </select>
            @error('permanent_district')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- permanent_district -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="permanent_municipality">Permanent Municipality*</label>
            <input type="text" class="form-control" id="permanent_municipality" placeholder="Enter Employee permanent_municipality" name="permanent_municipality" value="{{ !empty(old('permanent_municipality')) ? old('permanent_municipality') : $employee->permanent_municipality ?? '' }}">
            @error('permanent_municipality')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- permanent_municipality -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="permanent_ward_no">Permanent Ward_no*</label>
            <input type="text" class="form-control" id="permanent_ward_no" placeholder="Enter Employee permanent_ward_no" name="permanent_ward_no" value="{{ !empty(old('permanent_ward_no')) ? old('permanent_ward_no') : $employee->permanent_ward_no ?? '' }}">
            @error('permanent_ward_no')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- permanent_ward_no -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="permanent_tole">Permanent Tole*</label>
            <input type="text" class="form-control" id="permanent_tole" placeholder="Enter Employee permanent_tole" name="permanent_tole" value="{{ !empty(old('permanent_tole')) ? old('permanent_tole') : $employee->permanent_tole ?? '' }}">
            @error('permanent_tole')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- permanent_tole -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="temp_add_same_as_per_add">Temporary Add Same As Permanent*</label>
            <br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="temp_add_same_as_per_add" id="yes" value="1" 
                {{ (isset($employee) && $employee->temp_add_same_as_per_add == '1') ? 'checked':'' }}
                {{ old('temp_add_same_as_per_add') == '1' ? 'checked':'' }}>
                <label class="form-check-label" for="yes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="temp_add_same_as_per_add" id="no" value="0" 
                {{ (isset($employee) && $employee->temp_add_same_as_per_add == '0') ? 'checked':'' }}
                {{ old('temp_add_same_as_per_add') == '0' ? 'checked':'' }}>
                <label class="form-check-label" for="no">No</label>
            </div>
            @error('permanent_toletemp_add_same_as_per_add')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- temp_add_same_as_per_add -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="temporary_address">Temporary Address</label>
             <input type="text" class="form-control" id="temporary_address" placeholder="Enter Employee temporary Address" name="temporary_address" value="{{ !empty(old('temporary_address')) ? old('temporary_address') : $employee->temporary_address ?? '' }}">
            @error('temporary_address')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- tempoarary province -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="temporary_district">Temporary District</label>
            <input type="text" class="form-control" id="temporary_district" placeholder="Enter Employee temporary_district" name="temporary_district" value="{{ !empty(old('temporary_district')) ? old('temporary_district') : $employee->temporary_district ?? '' }}">
            @error('temporary_district')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- temporary_district -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="temporary_municipality">Temporary Municipality</label>
            <input type="text" class="form-control" id="temporary_municipality" placeholder="Enter Employee temporary_municipality" name="temporary_municipality" value="{{ !empty(old('temporary_municipality')) ? old('temporary_municipality') : $employee->temporary_municipality ?? '' }}">
            @error('temporary_municipality')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- temporary_municipality -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="temporary_ward_no">Temporary Ward No</label>
            <input type="text" class="form-control" id="temporary_ward_no" placeholder="Enter Employee temporary_ward_no" name="temporary_ward_no" value="{{ !empty(old('temporary_ward_no')) ? old('temporary_ward_no') : $employee->temporary_ward_no ?? '' }}">
            @error('temporary_ward_no')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- temporary_ward_no -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="temporary_tole">Temporary Tole</label>
            <input type="text" class="form-control" id="temporary_tole" placeholder="Enter Employee temporary_tole" name="temporary_tole" value="{{ !empty(old('temporary_tole')) ? old('temporary_tole') : $employee->temporary_tole ?? '' }}">
            @error('temporary_tole')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- temporary_tole -->
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="join_date">Join Date*</label>
            <input type="date" class="form-control" id="join_date" placeholder="Enter Employee join_date" name="join_date" value="{{ !empty(old('join_date')) ? old('join_date') : $employee->join_date ?? '' }}">
            @error('join_date')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- join_date -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="intern_trainee_ship_date">Intern/TraineeShip Date</label>
            <input type="date" class="form-control" id="intern_trainee_ship_date" placeholder="Enter Employee intern_trainee_ship_date" name="intern_trainee_ship_date" value="{{ !empty(old('intern_trainee_ship_date')) ? old('intern_trainee_ship_date') : $employee->intern_trainee_ship_date ?? '' }}">
            @error('intern_trainee_ship_date')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>  
    </div>
</div>
<!-- intern_trainee_ship_date -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="service_type">Service Type*</label>
            <select class="form-control" id="service_type" name="service_type">
                <option value="" disabled="disabled" selected="selected">-- Choose Service --</option>
                @forelse($serviceTypes as $serviceType)
                <option 
                    value="{{ $serviceType->id}}" 
                    {{ (!empty(old('service_type')) && old('service_type') == $serviceType->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->service_type == $serviceType->id && empty(old('service_type'))) ? 'selected' : '' }} 
                    >
                    {{ $serviceType->service_type_name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('serviceType_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- Service Type -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="manager_id">Manager</label>
            <input type="text" class="form-control" id="manager_id" placeholder="Enter Employee manager_id" name="manager_id" value="{{ !empty(old('manager_id')) ? old('manager_id') : $employee->manager_id ?? '' }}">
            @error('manager_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- manager_id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="designation_id">Designation*</label>
            <select class="form-control" id="designation_id" name="designation_id">
                <option value="" disabled="disabled" selected="selected">-- Choose Designation --</option>
                @forelse($designations as $designation)
                <option 
                    value="{{ $designation->id}}" 
                    {{ (!empty(old('designation_id')) && old('designation_id') == $designation->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->designation_id == $designation->id && empty(old('designation_id'))) ? 'selected' : '' }} 
                    >
                    {{ $designation->job_title_name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('designation_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- designation_id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="designation_change_date">Designation Change Date</label>
            <input type="date" class="form-control" id="designation_change_date" placeholder="Enter Employee designation_change_date" name="designation_change_date" value="{{ !empty(old('designation_change_date')) ? old('designation_change_date') : $employee->designation_change_date ?? '' }}">
            @error('designation_change_date')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- designation_change_date -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="organization_id">Organization*</label>
            <select class="form-control" id="organization_id" name="organization_id">
                <option value="" disabled="disabled" selected="selected">-- Choose Organization --</option>
                @forelse($organizations as $organization)
                <option 
                    value="{{ $organization->id}}" 
                    {{ (!empty(old('organization_id')) && old('organization_id') == $organization->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->organization_id == $organization->id && empty(old('organization_id'))) ? 'selected' : '' }} 
                    >
                    {{ $organization->name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('organization_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- organization_id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="unit_id">Unit*</label>
            <select class="form-control" id="unit_id" name="unit_id">
                <option value="" disabled="disabled" selected="selected">-- Choose Unit --</option>
                @forelse($units as $unit)
                <option 
                    value="{{ $unit->id}}" 
                    {{ (!empty(old('unit_id')) && old('unit_id') == $unit->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->unit_id == $unit->id && empty(old('unit_id'))) ? 'selected' : '' }} 
                    >
                    {{ $unit->unit_name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('unit_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- unit_id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="email">Email*</label>
            <input type="text" class="form-control" id="email" placeholder="Enter Employee email" name="email" value="{{ !empty(old('email')) ? old('email') : $employee->email ?? '' }}">
            @error('email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- email -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="username">Username*</label>
            <input type="text" class="form-control" id="username" placeholder="Enter Employee username" name="username" 
            value="{{ !empty(old('username')) ? old('username'): $employee->user->username ?? ''}}">
            @error('username')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- username -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="role">Role*</label>
            <select class="form-control" id="role" name="role">
                @if(Route::current()->uri != 'employee/edit/{id}')
                <option value="" disabled="disabled" selected="selected">-- Choose Role --</option>
                @endif
                @foreach($roles as $role)
                <option 
                    value="{{$role->id}}" 
                    {{ (!empty(old('role')) && old('role') == $role->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->role == $role->id && empty(old('role'))) ? 'selected' : '' }} 
                    >{{$role->authority}}</option>
                @endforeach
            </select>
            @error('role')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- role -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="shift_id">Shift*</label>
            <select class="form-control" id="shift_id" name="shift_id">
                <option value="" disabled="disabled" selected="selected">-- Choose Shift --</option>
                @forelse($shifts as $shift)
                <option 
                    value="{{ $shift->id}}" 
                    {{ (!empty(old('shift_id')) && old('shift_id') == $shift->id) ? 'selected': ''}}
                    {{ (isset($employee) && $employee->shift_id == $shift->id && empty(old('shift_id'))) ? 'selected' : '' }} 
                    >
                    {{ $shift->name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('shift_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- Shift -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="remarks">Employee Remarks</label>
            <input type="text" class="form-control" id="remarks" placeholder="Enter Employee remarks" name="remarks" value="{{ !empty(old('remarks')) ? old('remarks') : $employee->remarks ?? '' }}">
            @error('remarks')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- remarks -->

<hr>
<!-- emergency_contact -->
<fieldset>
    <legend>Emergency Contact</legend>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <label for="emg_first_name">First Name*</label>
                <input type="text" class="form-control" id="emg_first_name" placeholder="Enter Emergency Contact First Name" name="emg_first_name" value="{{ !empty(old('emg_first_name')) ? old('emg_first_name') : $employee->emergencyContact->first_name?? '' }}">
                @error('emg_first_name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

    <!-- emg_first_name -->

        <div class="col-md-6">
                <div class="mb-4">
                <label for="emg_middle_name">Middle Name</label>
                <input type="text" class="form-control" id="emg_middle_name" placeholder="Enter Emergency Contact Middle Name" name="emg_middle_name" value="{{ !empty(old('emg_middle_name')) ? old('emg_middle_name') : $employee->emergencyContact->middle_name?? '' }}">
                @error('emg_middle_name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    <!-- emg_middle_name -->

    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <label for="emg_last_name">Last Name*</label>
                <input type="text" class="form-control" id="emg_last_name" placeholder="Enter Emergency Contact Last Name" name="emg_last_name" value="{{ !empty(old('emg_last_name')) ? old('emg_last_name') : $employee->emergencyContact->last_name?? '' }}">
                @error('emg_last_name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

    <!-- emg_last_name -->

        <div class="col-md-6">
            <div class="mb-4">
                <label for="emg_relationship">Relationship*</label>
                <input type="text" class="form-control" id="emg_relationship" placeholder="Enter Emergency Contact First Name" name="emg_relationship" value="{{ !empty(old('emg_relationship')) ? old('emg_relationship') : $employee->emergencyContact->relationship ?? '' }}">
                @error('emg_relationship')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    
    <!-- relationship -->

    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <label for="emg_contact">Emergency Contact*</label>
                <input type="text" class="form-control" id="emg_contact" placeholder="Enter Emergency Contact First Name" name="emg_contact" value="{{ !empty(old('emg_contact')) ? old('emg_contact') : $employee->emergencyContact->phone_no?? '' }}">
                @error('emg_contact')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

    <!-- contact -->

        <div class="col-md-6">
            <div class="mb-4">
                <label for="emg_alternate_contact">Alternate Contact</label>
                <input type="text" class="form-control" id="emg_alternate_contact" placeholder="Enter Emergency Alternate_contact First Name" name="emg_alternate_contact" value="{{ !empty(old('emg_alternate_contact')) ? old('emg_alternate_contact') : $employee->emergencyContact->alternate_phone_no?? '' }}">
                @error('emg_alternate_contact')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>


    <!-- alternate_contact -->
</fieldset>
