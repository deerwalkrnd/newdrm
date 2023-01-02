<!-- Form Starts -->
<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="employee_id">Employee ID</label>
            <input type="number" disabled class="form-control" id="employee_id" placeholder="None" name="employee_id" value="{{ !empty(old('employee_id')) ? old('employee_id') : $employee->employee_id ?? '' }}">
            @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- Employee Id -->
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="first_name">First Name*</label>
            <input type="text" disabled class="form-control" id="first_name"  name="first_name" value="{{ ucfirst($employee->first_name) }}">
            @error('first_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- firstname -->
<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="middle_name">Middle Name</label>
            <input type="text" class="form-control"  disabled  id="middle_name" placeholder="None" name="middle_name" value="{{ !empty(old('middle_name')) ? old('middle_name') : ucfirst($employee->middle_name) ?? '' }}">
            @error('middle_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- middlename -->
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="last_name">Last Name*</label>
            <input type="text" class="form-control" id="last_name" disabled placeholder="None" name="last_name" value="{{ !empty(old('last_name')) ? old('last_name') : ucfirst($employee->last_name) ?? '' }}">
            @error('last_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- lastname -->
</div>   

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label for="date_of_birth" class="form-label">DOB*</label>
            <input type="date" class="form-control" disabled id="date_of_birth" placeholder="Not Provided" name="date_of_birth" value="{{ !empty(old('date_of_birth')) ? old('date_of_birth') : $employee->date_of_birth ?? '' }}">
            @error('date_of_birth')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- dob -->
  <div class="col-md-6">
        <div class="mb-4">
            <label  class="form-label" for="gender">Gender*</label>
            <input type="text" class="form-control" id="gender" disabled name="gender" value="{{ !empty(old('gender')) ? old('gender') : ucfirst($employee->gender) ?? '' }}">
            <!-- <br>
            <div class="form-check form-check-inline">
                <input disabled class="form-check-input" type="radio" name="gender" id="gender0" value="Female" 
                {{ (isset($employee) && $employee->gender == 'Female') ? 'checked':''}}
                {{ old('gender') == 'Female' ? 'checked':'' }}>
                <label class="form-check-label" for="gender0">Female</label>
            </div>
            <div class="form-check form-check-inline">
                <input disabled class="form-check-input" type="radio" name="gender" id="gender1" value="Male" 
                {{ (isset($employee) && $employee->gender == 'Male') ? 'checked':''}}
                {{ old('gender') == 'Male' ? 'checked':''}}>
                <label class="form-check-label" for="gender1">Male</label>
            </div>
            <div class="form-check form-check-inline">
                <input disabled class="form-check-input" type="radio" name="gender" id="gender2" value="Other" 
                {{ (isset($employee) && $employee->gender == 'Other') ? 'checked':'' }}
                {{ old('gender') == 'Other' ? 'checked':'' }}>
                <label class="form-check-label" for="gender2">Other</label>
            </div> -->
            @error('gender')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- gender -->

</div>


<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="marital_status">Marital Status*</label>
            <input type="text" class="form-control" id="marital_status" disabled name="marital_status" value="{{ !empty(old('marital_status')) ? old('marital_status') : ucfirst($employee->marital_status) ?? '' }}">
            @error('marital_status')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- marital-status -->

    @if ( ucfirst($employee->marital_status) == 'Divorced' ||  ucfirst($employee->marital_status) == 'Married')
        <div class="col-md-6" id="spouseNameBlock" >
        <div class="mb-4">
            <label  class="form-label" for="spouse_name">Spouse Name</label>
            <input type="text" class="form-control" id="spouse_name" disabled placeholder="Not Updated" name="spouse_name" value="{{ !empty(old('spouse_name')) ? old('spouse_name') : ucfirst($employee->spouse_name) ?? '' }}">
            @error('spouse_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    @endif
<!-- spouse_name --> 
</div>


<div class="row">
     <div class="col-md-6">
        <div class="mb-4">
            <label  class="form-label" for="grand_father">Grandfather Name</label>
            <input type="text" class="form-control" id="grand_father" placeholder="None" disabled name="grand_father" value="{{ !empty(old('grand_father')) ? old('grand_father') : ucfirst($employee->grand_father) ?? '' }}">
            @error('grand_father')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <!-- grand_father --> 

    <div class="col-md-6">
        <div class="mb-4">
            <label  class="form-label" for="father_name">Father Name</label>
            <input type="text" class="form-control" id="father_name" placeholder="None" disabled name="father_name" value="{{ !empty(old('father_name')) ? old('father_name') : ucfirst($employee->father_name) ?? '' }}">
            @error('father_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- father_name -->   
</div>


<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label  class="form-label" for="mother_name">Mother Name</label>
            <input type="text" class="form-control" id="mother_name" placeholder="None" disabled name="mother_name" value="{{ !empty(old('mother_name')) ? old('mother_name') : ucfirst($employee->mother_name) ?? '' }}">
            @error('mother_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- mother_name -->

    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="mobile">Mobile*</label>
            <input type="text" class="form-control" id="mobile" placeholder="None" disabled name="mobile" value="{{ !empty(old('mobile')) ? old('mobile') : $employee->mobile ?? '' }}">
            @error('mobile')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- mobile -->
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" class="form-label" for="alternative_mobile">Alternative Mobile</label>
            <input type="text" class="form-control" id="alternative_mobile" placeholder="None" disabled name="alternative_mobile" value="{{ !empty(old('alternative_mobile')) ? old('alternative_mobile') : $employee->alternative_mobile ?? '' }}">
            @error('alternative_mobile')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- alternative_mobile -->

    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="home_phone">Home Mobile</label>
            <input type="text" class="form-control" id="home_phone" placeholder="None" disabled name="home_phone" value="{{ !empty(old('home_phone')) ? old('home_phone') : $employee->home_phone ?? '' }}">
            @error('home_phone')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- home_phone -->
</div>


<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="image">Image</label>
            <input type="file" class="form-control" placeholder="None" disabled id="image" name="image">
            @error('image')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- image -->

    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="alter_email">Personal Email*</label>
            <input type="text" class="form-control" id="alter_email" placeholder="None" disabled name="alter_email" value="{{ !empty(old('alter_email')) ? old('alter_email') : $employee->alter_email ?? '' }}">
            @error('alter_email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

<!-- alter_email -->
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="cv">Resume (PDF)</label>
            <input type="file" class="form-control" placeholder="None" disabled id="cv" name="cv">
            @error('cv')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- resume -->

    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="country">Country*</label>
            <!-- <input type="text" placeholder="None" disabled value="{{ $employee->country }}"> -->
            <select class="form-control" id="country" name="country" disabled>
                @if(isset($employee)) $country = $employee->country @endif
                @include('layouts.country_options')
            </select>
            @error('country')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- country -->
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="nationality">Nationality</label>
            <input type="text" class="form-control" id="nationality" placeholder="None" disabled name="nationality" value="{{ !empty(old('nationality')) ? old('nationality') : ucfirst($employee->nationality) ?? '' }}">
            @error('nationality')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- nationality -->

    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="blood_group">Blood Group</label>
            <input type="text" class="form-control" placeholder="None" disabled id="blood_group"  name="blood_group" value="{{ !empty(old('blood_group')) ? old('blood_group') : $employee->blood_group ?? '' }}">
            @error('blood_group')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- blood_group -->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label  class="form-label" for="profile">Profile</label>
            <textarea class="form-control" id="profile" placeholder="None" disabled name="profile">{{ !empty(old('profile')) ? old('profile') : ucfirst($employee->profile) ?? '' }}</textarea>
            @error('profile')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- profile -->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="permanent_address">Province*</label>
            <select disabled class="form-control" id="permanent_address" name="permanent_address">
                <option value="" disabled="disabled" selected="selected">{{ ucfirst($employee->province->province_name) }}</option>
            </select>
        </div>
    </div>
</div>

<!-- province -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="permanent_district">Permanent District*</label>
            <select disabled class="district-livesearch form-control p-3" name="permanent_district" id="permanent_district" data-placeholder="-- Choose District --">
            <option value="" disabled selected="selected">{{ ucfirst($employee->district->district_name) }}</option>
            </select>
        </div>
    </div>
</div>
<!-- permanent_district -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="permanent_municipality">Permanent Municipality*</label>
            <input type="text" class="form-control" id="permanent_municipality" placeholder="None" disabled name="permanent_municipality" value="{{ !empty(old('permanent_municipality')) ? old('permanent_municipality') : ucfirst($employee->permanent_municipality) ?? '' }}">
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
            <label class="form-label" for="permanent_ward_no">Permanent Ward_no*</label>
            <input type="text" class="form-control" id="permanent_ward_no" placeholder="None" disabled name="permanent_ward_no" value="{{ !empty(old('permanent_ward_no')) ? old('permanent_ward_no') : $employee->permanent_ward_no ?? '' }}">
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
            <label class="form-label" for="permanent_tole">Permanent Tole*</label>
            <input type="text" class="form-control" id="permanent_tole" placeholder="None" disabled name="permanent_tole" value="{{ !empty(old('permanent_tole')) ? old('permanent_tole') : ucfirst($employee->permanent_tole) ?? '' }}">
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
            <label class="form-label" for="temp_add_same_as_per_add">Is Temporary Address Same As Permanent Address*</label>
            <br>
            <div class="form-check form-check-inline">
                <input disabled class="form-check-input" type="radio" name="temp_add_same_as_per_add" id="yes" value="1" 
                {{ (isset($employee) && $employee->temp_add_same_as_per_add == '1') ? 'checked':'' }}
                {{ old('temp_add_same_as_per_add') == '1' ? 'checked':'' }}
                onclick="$('#tempBlock').hide()">
                <label class="form-check-label" for="yes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input  disabled class="form-check-input" type="radio" name="temp_add_same_as_per_add" id="no" value="0" 
                {{ (isset($employee) && $employee->temp_add_same_as_per_add == '0') ? 'checked':'' }}
                {{ old('temp_add_same_as_per_add') == '0' ? 'checked':'' }}
                onclick="$('#tempBlock').show()">
                <label class="form-check-label" for="no">No</label>
            </div>
            @error('permanent_toletemp_add_same_as_per_add')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- temp_add_same_as_per_add -->

<div id="tempBlock">
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="temporary_address">Temporary Address*</label>
             <input type="text" class="form-control" placeholder="None" disabled id="temporary_address" placeholder="Enter Employee temporary Address" name="temporary_address" value="{{ !empty(old('temporary_address')) ? old('temporary_address') : ucfirst($employee->temporary_address) ?? '' }}">
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
            <label class="form-label" for="temporary_district">Temporary District*</label>
            <input type="text" class="form-control" placeholder="None" disabled id="temporary_district" placeholder="Enter Employee temporary_district" name="temporary_district" value="{{ !empty(old('temporary_district')) ? old('temporary_district') : ucfirst($employee->temporary_district) ?? '' }}">
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
            <label class="form-label" for="temporary_municipality">Temporary Municipality*</label>
            <input type="text" class="form-control" id="temporary_municipality" placeholder="None" disabled name="temporary_municipality" value="{{ !empty(old('temporary_municipality')) ? old('temporary_municipality') : ucfirst($employee->temporary_municipality) ?? '' }}">
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
            <label class="form-label" for="temporary_ward_no">Temporary Ward No*</label>
            <input type="text" class="form-control" id="temporary_ward_no" placeholder="None" disabled name="temporary_ward_no" value="{{ !empty(old('temporary_ward_no')) ? old('temporary_ward_no') : $employee->temporary_ward_no ?? '' }}">
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
            <label class="form-label" for="temporary_tole">Temporary Tole*</label>
            <input type="text" class="form-control" id="temporary_tole" placeholder="None" disabled name="temporary_tole" value="{{ !empty(old('temporary_tole')) ? old('temporary_tole') : ucfirst($employee->temporary_tole) ?? '' }}">
            @error('temporary_tole')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- temporary_tole -->
</div>
<!-- tempBlock Ends -->
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="join_date">Join Date* (AD)</label>
            <input type="date" class="form-control" id="join_date" placeholder="None" disabled name="join_date" value="{{ !empty(old('join_date')) ? old('join_date') : $employee->join_date ?? '' }}">
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
            <label class="form-label" for="intern_trainee_ship_date">Intern/TraineeShip Date</label>
            <input type="date" class="form-control" id="intern_trainee_ship_date" placeholder="None" disabled name="intern_trainee_ship_date" value="{{ !empty(old('intern_trainee_ship_date')) ? old('intern_trainee_ship_date') : $employee->intern_trainee_ship_date ?? '' }}">
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
            <label class="form-label" for="service_type">Service Type*</label>
            <select disabled class="form-control" id="service_type" name="service_type">
                <option value="" disabled="disabled" selected="selected">{{ ucfirst($employee->serviceType->service_type_name) }}</option>
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
            <!-- <label class="form-label" for="manager_id">Manager</label>
            <input type="text" class="form-control" id="manager_id" placeholder="Enter Employee manager_id" name="manager_id" value="{{ !empty(old('manager_id')) ? old('manager_id') : $employee->manager_id ?? '' }}">
            @error('manager_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror -->


            <label class="form-label" for="manager_id">Manager</label>
            <select disabled class="form-control" id="manager_id" name="manager_id">
                <option value="" disabled selected="selected">{{ $employee->manager ? (ucfirst($employee->manager->first_name).' '.ucfirst($employee->manager->middle_name).' '.ucfirst($employee->manager->last_name)) : 'None' }}</option>
            </select>
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
            <label class="form-label" for="manager_change_date">Manager Change Date</label>
            <input type="date" class="form-control" id="manager_change_date" placeholder="None" disabled name="manager_change_date" value="{{ $employee->manager_change_date ?? '' }}">
            @error('manager_change_date')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- manager_change_date -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="designation_id">Designation*</label>
            <select disabled class="form-control" id="designation_id" name="designation_id">
                <option value="" disabled="disabled" selected="selected">{{ $employee->designation->job_title_name }}</option>
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
            <label class="form-label" for="designation_change_date">Designation Change Date</label>
            <input type="date" class="form-control" id="designation_change_date" placeholder="None" disabled name="designation_change_date" value="{{ $employee->designation_change_date ?? '' }}">
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
            <label class="form-label" for="organization_id">Organization*</label>
            <select disabled class="form-control" id="organization_id" name="organization_id">
                <option value="" disabled="disabled" selected="selected">{{ ucfirst($employee->organization->name) }}</option>
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
            <label class="form-label" for="unit_id">Unit*</label>
            <select disabled class="form-control" id="unit_id" name="unit_id">
                <option value="" disabled="disabled" selected="selected">{{ ucfirst($employee->unit->unit_name) }}</option>
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
            <label class="form-label" for="department_id">Department*</label>
            <select disabled class="form-control" id="department_id" name="department_id">
                <option value="" disabled="disabled" selected="selected">{{ ucfirst($employee->department->name) }}</option>
            </select>
            @error('department_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- department_id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="department_change_date">Department Change Date</label>
            <input type="date" class="form-control" id="department_change_date" placeholder="None" disabled name="department_change_date" value="{{ $employee->department_change_date ?? '' }}">
            @error('department_change_date')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- department_change_date -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="email">Email*</label>
            <input type="text" class="form-control" id="email" placeholder="None" disabled name="email" value="{{ !empty(old('email')) ? old('email') : $employee->email ?? '' }}">
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
            <label class="form-label" for="username">Username*</label>
            <input type="text" class="form-control" id="username" placeholder="None" disabled name="username" 
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
            <label class="form-label" for="role">Role*</label>
            <select disabled class="form-control" id="role" name="role">
                <option value="" disabled="disabled" selected="selected">{{ ucfirst($employee->user->role->authority) }}</option>
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
            <label class="form-label" for="shift_id">Shift*</label>
            <select disabled class="form-control" id="shift_id" name="shift_id">
                <option value="" disabled="disabled" selected="selected">{{ucfirst($employee->shift->name)}}</option>
            </select>
            @error('shift_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- Shift -->

<div class="row"  id="shift_time" style="display:none;">
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="start_time">Start Time</label>
            <input disabled type="time" class="form-control" id="start_time" placeholder="Enter Employee\'s Shift Start time" name="start_time" value="{{ isset($employee->start_time) ? date('H:i',strtotime($employee->start_time)):''}}">
            @error('start_time')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
<!-- Start Time -->
    <div class="col-md-6">
        <div class="mb-4">
            <label class="form-label" for="end_time">End Time</label>
            <input disabled type="time" class="form-control" id="end_time" placeholder="Enter Employee\'s Shift End time" name="end_time" value="{{ isset($employee->end_time)?date('H:i',strtotime($employee->end_time)):''}}">
            @error('end_time')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- End Time -->
<!-- Shift Timeif required -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="remarks">Employee Remarks</label>
            <input type="text" class="form-control" id="remarks" placeholder="None" disabled name="remarks" value="{{ !empty(old('remarks')) ? old('remarks') : ucfirst($employee->remarks) ?? '' }}">
            @error('remarks')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- remarks -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="pan_number">Permanent Account Number (PAN)</label>
            <input type="text" class="form-control" id="pan_number" disabled placeholder="Not Updated" name="pan_number" value="{{ !empty(old('pan_number')) ? old('pan_number') : $employee->pan_number ?? '' }}">
            @error('pan_number')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- pan no. -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="cit_number">Citizen Investment Trust (CIT) Number </label>
            <input type="text" class="form-control" id="cit_number" disabled placeholder="Not Updated" name="cit_number" value="{{ !empty(old('cit_number')) ? old('cit_number') : $employee->cit_number ?? '' }}">
            @error('cit_number')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- CIT no. -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="ssf_id">Social Security Fund (SSF) Id</label>
            <input type="text" class="form-control" id="ssf_id" disabled placeholder="Not Updated" name="ssf_id" value="{{ !empty(old('ssf_id')) ? old('ssf_id') : $employee->ssf_id ?? '' }}">
            @error('ssf_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- SSF ID -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="nibl_account_number">NIBL Account Number</label>
            <input type="text" class="form-control" id="nibl_account_number" disabled placeholder="Not Updated" name="nibl_account_number" value="{{ !empty(old('nibl_account_number')) ? old('nibl_account_number') : $employee->nibl_account_number ?? '' }}">
            @error('nibl_account_number')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- NIBL Account no. -->

<hr>
<!-- emergency_contact -->
<fieldset>
    <legend>Emergency Contact</legend>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <label class="form-label" for="emg_first_name">First Name*</label>
                <input type="text" class="form-control" id="emg_first_name" placeholder="None" disabled name="emg_first_name" value="{{ isset($employee->emergencyContact)? $employee->emergencyContact->first_name:'None' }}">
                @error('emg_first_name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

    <!-- emg_first_name -->

        <div class="col-md-6">
                <div class="mb-4">
                <label class="form-label" for="emg_middle_name">Middle Name</label>
                <input type="text" class="form-control" id="emg_middle_name"placeholder="None" disabled name="emg_middle_name" value="{{ isset($employee->emergencyContact)? $employee->emergencyContact->middle_name:'None' }}">
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
                <label class="form-label" for="emg_last_name">Last Name*</label>
                <input type="text" class="form-control" id="emg_last_name" placeholder="None" disabled name="emg_last_name" value="{{ isset($employee->emergencyContact)? $employee->emergencyContact->last_name:'None' }}">
                @error('emg_last_name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

    <!-- emg_last_name -->

        <div class="col-md-6">
            <div class="mb-4">
                <label class="form-label" for="emg_relationship">Relationship*</label>
                <input type="text" class="form-control" id="emg_relationship" placeholder="None" disabled name="emg_relationship" value="{{ isset($employee->emergencyContact)? $employee->emergencyContact->relationship:'None' }}">
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
                <label class="form-label" for="emg_contact">Emergency Contact*</label>
                <input type="text" class="form-control" id="emg_contact" placeholder="None" disabled name="emg_contact" value="{{ isset($employee->emergencyContact)? $employee->emergencyContact->phone_no:'None' }}">
                @error('emg_contact')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

    <!-- contact -->

        <div class="col-md-6">
            <div class="mb-4">
                <label class="form-label" for="emg_alternate_contact">Alternate Contact</label>
                <input type="text" class="form-control" id="emg_alternate_contact" placeholder="None" disabled name="emg_alternate_contact" value="{{ isset($employee->emergencyContact)? $employee->emergencyContact->alternate_phone_no:'None' }}">
                @error('emg_alternate_contact')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>


    <!-- alternate_contact -->
</fieldset>


<hr>
<!-- available documents -->
<fieldset>
    <legend>Available Documents</legend>
    
    <div class="row">
        @forelse ($employee->fileUploads as $fileUpload)
            <div class="col-md-6 ">
                <div class="mb-4 row">
                    <div class="col-md-6">
                        <label class="form-label" for="emg_first_name">{{ $fileUpload->fileCategory->category_name }}</label>
                    </div>
                    <div class="col-md-6 align-right">
                        <a href="/file-upload/download/{{ $fileUpload->id }}"><i class="fa fa-download" aria-hidden="true"></i></a> 
                    </div>
                </div>
            </div>
        @empty
            <p  class="text-center text-danger">------ No Uploaded File Found ------</p>
        @endforelse
        
    </div>
    <!-- available files end -->
</fieldset>
