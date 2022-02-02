<!-- @if(1==2)
<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="employee_id">Employee Name*</label> -->            
            <!-- <select class="manager-livesearch form-control p-3" name="employee_id" id="employee_id" placeholder="-- Choose Employee --">
                @if(!empty(old('employee_id')))
                    <option value="{{ old('employee_id') }}" selected="selected">j{{ old('employee_name') }}</option>
                @elseif(isset($manager) && !empty($manager->employee))
                    <option value="{{ $manager->employee->id }}" selected="selected">s{{ $manager->employee->name }}</option>
                @endif
            </select> -->

            <!-- <select class="form-control" id="employee_id" name="employee_id">
                <option value="" disabled="disabled" selected="selected">-- Choose Employee --</option>
                @forelse($employees as $employee)
                <option 
                    value="{{ $employee->id}}" 
                    {{ (!empty(old('employee_id')) && old('employee_id') == $employee->id) ? 'selected': ''}}
                    {{ (isset($manager) && $manager->employee_id == $employee->id && empty(old('employee_id'))) ? 'selected' : '' }} 
                    >
                    {{ $employee->first_name.' '.$employee->last_name}}
                </option>
                @empty
                @endforelse
            </select> -->
            <!-- @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
@endif -->
<!-- Employee Name -->


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="employee_id">Employee Name</label>
            <select class="manager-livesearch form-control p-3" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --">
                @if(!empty(old('employee_id')))
                    <option value="{{ old('employee_id') }}" selected="selected">{{ old('employee_id') }}</option>
                @elseif(isset($manager) && !empty($manager->employee))
                    <option value="{{ $manager->employee->id }}" selected="selected">{{ $manager->employee->first_name.' '.substr($manager->employee->middle_name,1).' '.$manager->employee->last_name }}</option>
                @endif
            </select>
            @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- Employee Id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="is_active">Manager Status*</label>
            <select class="form-control" id="is_active" name="is_active">
                <option value="" disabled="disabled" selected="selected">-- Choose Status --</option>
                <option 
                    value="active" 
                    {{ (!empty(old('is_active')) && old('is_active') == 'active') ? 'selected': ''}}
                    {{ (isset($manager) && $manager->is_active == $manager->is_active && empty(old('is_active'))) ? 'selected' : '' }} 
                    >
                    Active
                </option>
                <option 
                    value="inactive" 
                    {{ (!empty(old('is_active')) && old('is_active') == 'inactive') ? 'selected': ''}}
                    {{ (isset($manager) && $manager->is_active == $manager->is_active && empty(old('is_active'))) ? 'selected' : '' }} 
                    >
                    Inactive
                </option>
            </select>
            @error('is_active')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- Active-status -->

