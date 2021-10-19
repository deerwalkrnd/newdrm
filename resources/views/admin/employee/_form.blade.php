<div class="form-group">
    <label for="employee_id">Employee Id</label>
    <input type="number" class="form-control" id="employee_id" placeholder="Enter Employee Id" name="employee_id" value="{{ !empty(old('employee_id')) ? old('employee_id') : $employee->employee_id ?? '' }}" min=1>
    @error('employee_id')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- employee id -->

<div class="form-group">
    <label for="first_name">First Name*</label>
    <input type="text" class="form-control" id="first_name" placeholder="Enter Employee First Name" name="first_name" value="{{ !empty(old('first_name')) ? old('first_name') : $employee->first_name ?? '' }}">
    @error('first_name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- firstname -->

<div class="form-group">
    <label for="last_name">Last Name*</label>
    <input type="text" class="form-control" id="last_name" placeholder="Enter Employee Last Name" name="last_name" value="{{ !empty(old('last_name')) ? old('last_name') : $employee->last_name ?? '' }}">
    @error('last_name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- lastname -->

<div class="form-group">
    <label for="middle_name">Middle Name</label>
    <input type="text" class="form-control" id="middle_name" placeholder="Enter Employee Middle Name" name="middle_name" value="{{ !empty(old('middle_name')) ? old('middle_name') : $employee->middle_name ?? '' }}">
    @error('middle_name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- middlename -->

<div class="form-group">
    <label for="date_of_birth">DOB*</label>
    <input type="date" class="form-control" id="date_of_birth" placeholder="Enter Employee DOB" name="date_of_birth" value="{{ !empty(old('date_of_birth')) ? old('date_of_birth') : $employee->date_of_birth ?? '' }}">
    @error('date_of_birth')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- dob -->

<div class="form-group">
    <label for="marital_status">Marital Status*</label>
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
<!-- marital-status -->


<div class="form-group">
    <label for="father_name">Gender*</label>
    <br>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender0" value="Female" 
        {{ (isset($employee) && $employee->gender == 'Female') ?? 'checked' }}
        {{ old('gender') == 'Female' ?? 'checked' }}>
        <label class="form-check-label" for="gender0">Female</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender1" value="Male" 
        {{ (isset($employee) && $employee->gender == 'Male') ?? 'checked'}}
        {{ old('gender') == 'Male' ?? 'checked' }}>
        <label class="form-check-label" for="gender1">Male</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender2" value="Other" 
        {{ (isset($employee) && $employee->gender == 'Other') ?? 'checked' }}
        {{ old('gender') == 'Other' ?? 'checked' }}>
        <label class="form-check-label" for="gender2">Other</label>
    </div>
    @error('gender')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>


<!-- gender -->

<div class="form-group">
    <label for="father_name">Father Name</label>
    <input type="text" class="form-control" id="father_name" placeholder="Enter Employee Father Name" name="father_name" value="{{ !empty(old('father_name')) ? old('father_name') : $employee->father_name ?? '' }}">
    @error('father_name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- father_name -->

<div class="form-group">
    <label for="mother_name">Mother Name</label>
    <input type="text" class="form-control" id="mother_name" placeholder="Enter Employee Mother Name" name="mother_name" value="{{ !empty(old('mother_name')) ? old('mother_name') : $employee->mother_name ?? '' }}">
    @error('mother_name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- mother_name -->

<div class="form-group">
    <label for="grand_father">Grandfather Name</label>
    <input type="text" class="form-control" id="grand_father" placeholder="Enter Employee grand_father" name="grand_father" value="{{ !empty(old('grand_father')) ? old('grand_father') : $employee->grand_father ?? '' }}">
    @error('grand_father')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- grand_father -->

<div class="form-group">
    <label for="mobile">Mobile*</label>
    <input type="text" class="form-control" id="mobile" placeholder="Enter Employee mobile" name="mobile" value="{{ !empty(old('mobile')) ? old('mobile') : $employee->mobile ?? '' }}">
    @error('mobile')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- mobile -->

<div class="form-group">
    <label for="alternative_mobile">Alternative Mobile</label>
    <input type="text" class="form-control" id="alternative_mobile" placeholder="Enter Employee alternative_mobile" name="alternative_mobile" value="{{ !empty(old('alternative_mobile')) ? old('alternative_mobile') : $employee->alternative_mobile ?? '' }}">
    @error('alternative_mobile')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- alternative_mobile -->

<div class="form-group">
    <label for="home_phone">Home Mobile</label>
    <input type="text" class="form-control" id="home_phone" placeholder="Enter Employee home_phone" name="home_phone" value="{{ !empty(old('home_phone')) ? old('home_phone') : $employee->home_phone ?? '' }}">
    @error('home_phone')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- home_phone -->

<div class="form-group">
    <label for="image">Image</label>
    <input type="file" class="form-control" id="image" name="image">
    @error('image')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- image -->

<div class="form-group">
    <label for="alter_email">Personal Email*</label>
    <input type="text" class="form-control" id="alter_email" placeholder="Enter Employee alter_email" name="alter_email" value="{{ !empty(old('alter_email')) ? old('alter_email') : $employee->alter_email ?? '' }}">
    @error('alter_email')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- alter_email -->

<div class="form-group">
    <label for="cv">CV</label>
    <input type="file" class="form-control" id="cv" name="cv">
    @error('cv')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- cv -->

<div class="form-group">
    <label for="country">Country*</label>
    @if(isset($employee)) $country = $employee->country @endif
    <select class="form-control" id="country" name="country">
        @include('layouts.country_options')
    </select>
    @error('country')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- country -->

<div class="form-group">
    <label for="nationality">Nationality</label>
    <input type="text" class="form-control" id="nationality" placeholder="Enter Employee nationality" name="nationality" value="{{ !empty(old('nationality')) ? old('nationality') : $employee->nationality ?? '' }}">
    @error('nationality')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- nationality -->

<div class="form-group">
    <label for="profile">Profile</label>
    <textarea class="form-control" id="profile" placeholder="Enter Employee profile" name="profile">{{ !empty(old('profile')) ? old('profile') : $employee->profile ?? '' }}</textarea>
    @error('profile')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- profile -->

<div class="form-group">
    <label for="blood_group">Blood_group</label>
    <input type="text" class="form-control" id="blood_group" placeholder="Enter Employee blood group" name="blood_group" value="{{ !empty(old('blood_group')) ? old('blood_group') : $employee->blood_group ?? '' }}">
    @error('blood_group')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- blood_group -->

<div class="form-group">
    <label for="permanent_address">Province*</label>
    <select class="form-control" id="permanent_address" name="permanent_address">
        <option value="" disabled="disabled" selected="selected">-- Choose Province --</option>
        <option 
            value="Province No. 1" 
            {{ (!empty(old('permanent_address')) && old('permanent_address') == 'Province No. 1') ? 'selected': ''}}
            {{ (isset($employee) && $employee->permanent_address == 'Province No. 1' && empty(old('permanent_address'))) ? 'selected' : '' }} 
            >Province No. 1</option>
        <option 
            value="Province No. 2" 
            {{ (!empty(old('permanent_address')) && old('permanent_address') == 'Province No. 2') ? 'selected': ''}}
            {{ (isset($employee) && $employee->permanent_address == 'Province No. 2' && empty(old('permanent_address'))) ? 'selected' : '' }} 
            >Province No. 2</option>
        <option 
            value="Province No. 3" 
            {{ (!empty(old('permanent_address')) && old('permanent_address') == 'Province No. 3') ? 'selected': ''}}
            {{ (isset($employee) && $employee->permanent_address == 'Province No. 3' && empty(old('permanent_address'))) ? 'selected' : '' }} 
            >Province No. 3</option>
        <option 
            value="Gandaki Pradesh" 
            {{ (!empty(old('permanent_address')) && old('permanent_address') == 'Gandaki Pradesh') ? 'selected': ''}}
            {{ (isset($employee) && $employee->permanent_address == 'Gandaki Pradesh' && empty(old('permanent_address'))) ? 'selected' : '' }} 
            >Gandaki Pradesh</option>
        <option 
            value="Province No. 5" 
            {{ (!empty(old('permanent_address')) && old('permanent_address') == 'Province No. 5') ? 'selected': ''}}
            {{ (isset($employee) && $employee->permanent_address == 'Province No. 5' && empty(old('permanent_address'))) ? 'selected' : '' }} 
            >Province No. 5</option>
        <option 
            value="Karnali Pradesh" 
            {{ (!empty(old('permanent_address')) && old('permanent_address') == 'Karnali Pradesh') ? 'selected': ''}}
            {{ (isset($employee) && $employee->permanent_address == 'Karnali Pradesh' && empty(old('permanent_address'))) ? 'selected' : '' }} 
            >Karnali Pradesh</option>
        <option 
            value="Sudurpaschim Pradesh" 
            {{ (!empty(old('permanent_address')) && old('permanent_address') == 'Sudurpaschim Pradesh') ? 'selected': ''}}
            {{ (isset($employee) && $employee->permanent_address == 'Sudurpaschim Pradesh' && empty(old('permanent_address'))) ? 'selected' : '' }} 
            >Sudurpaschim Pradesh</option>
    </select>
    @error('permanent_address')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- province -->

<div class="form-group">
    <label for="permanent_district">Permanent District*</label>
    <input type="text" class="form-control" id="permanent_district" placeholder="Enter Employee permanent_district" name="permanent_district" value="{{ !empty(old('permanent_district')) ? old('permanent_district') : $employee->permanent_district ?? '' }}">
    @error('permanent_district')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- permanent_district -->

<div class="form-group">
    <label for="permanent_municipality">Permanent Municipality*</label>
    <input type="text" class="form-control" id="permanent_municipality" placeholder="Enter Employee permanent_municipality" name="permanent_municipality" value="{{ !empty(old('permanent_municipality')) ? old('permanent_municipality') : $employee->permanent_municipality ?? '' }}">
    @error('permanent_municipality')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- permanent_municipality -->

<div class="form-group">
    <label for="permanent_ward_no">Permanent Ward_no*</label>
    <input type="text" class="form-control" id="permanent_ward_no" placeholder="Enter Employee permanent_ward_no" name="permanent_ward_no" value="{{ !empty(old('permanent_ward_no')) ? old('permanent_ward_no') : $employee->permanent_ward_no ?? '' }}">
    @error('permanent_ward_no')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- permanent_ward_no -->

<div class="form-group">
    <label for="permanent_tole">Permanent Tole*</label>
    <input type="text" class="form-control" id="permanent_tole" placeholder="Enter Employee permanent_tole" name="permanent_tole" value="{{ !empty(old('permanent_tole')) ? old('permanent_tole') : $employee->permanent_tole ?? '' }}">
    @error('permanent_tole')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- permanent_tole -->

<div class="form-group">
    <label for="permanent_toletemp_add_same_as_per_add">Temporary Add Same As Permanent*</label>
    <br>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="permanent_toletemp_add_same_as_per_add" id="yes" value="1" 
        {{ (isset($employee) && $employee->permanent_toletemp_add_same_as_per_add == '1') ?? 'checked' }}
        {{ old('permanent_toletemp_add_same_as_per_add') == '1' ?? 'checked' }}>
        <label class="form-check-label" for="yes">Yes</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="permanent_toletemp_add_same_as_per_add" id="no" value="0" 
        {{ (isset($employee) && $employee->permanent_toletemp_add_same_as_per_add == '0') ?? 'checked' }}
        {{ old('permanent_toletemp_add_same_as_per_add') == '0' ?? 'checked' }}>
        <label class="form-check-label" for="no">No</label>
    </div>
    @error('permanent_toletemp_add_same_as_per_add')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- temp_add_same_as_per_add -->

<div class="form-group">
    <label for="temporary_address">Temporary Address</label>
    <input type="text" class="form-control" id="temporary_address" placeholder="Enter Employee temporary Address" name="temporary_address" value="{{ !empty(old('temporary_address')) ? old('temporary_address') : $employee->temporary_address ?? '' }}">
    @error('temporary_address')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- tempoarary province -->

<div class="form-group">
    <label for="temporary_district">Temporary District</label>
    <input type="text" class="form-control" id="temporary_district" placeholder="Enter Employee temporary_district" name="temporary_district" value="{{ !empty(old('temporary_district')) ? old('temporary_district') : $employee->temporary_district ?? '' }}">
    @error('temporary_district')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- temporary_district -->

<div class="form-group">
    <label for="temporary_municipality">Temporary Municipality</label>
    <input type="text" class="form-control" id="temporary_municipality" placeholder="Enter Employee temporary_municipality" name="temporary_municipality" value="{{ !empty(old('temporary_municipality')) ? old('temporary_municipality') : $employee->temporary_municipality ?? '' }}">
    @error('temporary_municipality')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- temporary_municipality -->

<div class="form-group">
    <label for="temporary_ward_no">Temporary Ward No</label>
    <input type="text" class="form-control" id="temporary_ward_no" placeholder="Enter Employee temporary_ward_no" name="temporary_ward_no" value="{{ !empty(old('temporary_ward_no')) ? old('temporary_ward_no') : $employee->temporary_ward_no ?? '' }}">
    @error('temporary_ward_no')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- temporary_ward_no -->

<div class="form-group">
    <label for="temporary_tole">Temporary Tole</label>
    <input type="text" class="form-control" id="temporary_tole" placeholder="Enter Employee temporary_tole" name="temporary_tole" value="{{ !empty(old('temporary_tole')) ? old('temporary_tole') : $employee->temporary_tole ?? '' }}">
    @error('temporary_tole')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- temporary_tole -->
<hr>
<div class="form-group">
    <label for="join_date">Join Date</label>
    <input type="date" class="form-control" id="join_date" placeholder="Enter Employee join_date" name="join_date" value="{{ !empty(old('join_date')) ? old('join_date') : $employee->join_date ?? '' }}">
    @error('join_date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- join_date -->

<div class="form-group">
    <label for="intern_trainee_ship_date">Intern/TraineeShip Date</label>
    <input type="date" class="form-control" id="intern_trainee_ship_date" placeholder="Enter Employee intern_trainee_ship_date" name="intern_trainee_ship_date" value="{{ !empty(old('intern_trainee_ship_date')) ? old('intern_trainee_ship_date') : $employee->intern_trainee_ship_date ?? '' }}">
    @error('intern_trainee_ship_date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- intern_trainee_ship_date -->

<div class="form-group">
    <label for="manager_id">Manager</label>
    <input type="text" class="form-control" id="manager_id" placeholder="Enter Employee manager_id" name="manager_id" value="{{ !empty(old('manager_id')) ? old('manager_id') : $employee->manager_id ?? '' }}">
    @error('manager_id')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- manager_id -->

<div class="form-group">
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
<!-- designation_id -->

<div class="form-group">
    <label for="designation_change_date">Designation Change Date</label>
    <input type="date" class="form-control" id="designation_change_date" placeholder="Enter Employee designation_change_date" name="designation_change_date" value="{{ !empty(old('designation_change_date')) ? old('designation_change_date') : $employee->designation_change_date ?? '' }}">
    @error('designation_change_date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- designation_change_date -->

<div class="form-group">
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
<!-- organization_id -->

<div class="form-group">

{{ $units }}
    <label for="unit_id">Unit</label>
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
<!-- unit_id -->

<div class="form-group">
    <label for="email">Email*</label>
    <input type="text" class="form-control" id="email" placeholder="Enter Employee email" name="email" value="{{ !empty(old('email')) ? old('email') : $employee->email ?? '' }}">
    @error('email')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- email -->

<div class="form-group">
    <label for="emp_shift">Employee Shift*</label>
    <input type="text" class="form-control" id="emp_shift" placeholder="Enter Employee emp_shift" name="emp_shift" value="{{ !empty(old('emp_shift')) ? old('emp_shift') : $employee->emp_shift ?? '' }}">
    @error('emp_shift')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- emp_shift -->

<div class="form-group">
    <label for="remarks">Employee Remarks</label>
    <input type="text" class="form-control" id="remarks" placeholder="Enter Employee remarks" name="remarks" value="{{ !empty(old('remarks')) ? old('remarks') : $employee->remarks ?? '' }}">
    @error('remarks')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- remarks -->