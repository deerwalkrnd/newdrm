<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="employee_id">Employee</label>
            <select class="form-control" id="employee_id" name="employee_id">
                <option value="" disabled="disabled" selected="selected">-- Choose employee --</option>
                @forelse($employees as $employee)
                @if(\Auth::user()->role->authority == 'employee'){
                    <option 
                    selected
                    value="{{ \Auth::user()->employee_id}}" 
                    {{ (!empty(old('employee_id')) && old('employee_id') == $employee->id) ? 'selected': ''}}
                    {{ (isset($fileUpload) && $fileUpload->employee_id == $employee->id && empty(old('employee_id'))) ? 'selected' : '' }} 
                    >
                    {{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}
                </option>
                @break
                }@endif
                <option 
                    value="{{ $employee->id}}" 
                    {{ (!empty(old('employee_id')) && old('employee_id') == $employee->id) ? 'selected': ''}}
                    {{ (isset($fileUpload) && $fileUpload->employee_id == $employee->id && empty(old('employee_id'))) ? 'selected' : '' }} 
                    >
                    {{ $employee->first_name.' '.substr($employee->middle_name,0,1).' '.$employee->last_name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- select employee  -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="file">File Upload</label>
            <input type="file" class="form-control" id="file" placeholder="" name="file" >
            @error('file')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- file Upload -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="file_category_id">File Category</label>
            <select class="form-control" id="file_category_id" name="file_category_id">
                <option value="" disabled="disabled" selected="selected">-- Choose File Category --</option>
                @forelse($fileCategories as $fileCategory)
                <option 
                    value="{{ $fileCategory->id}}" 
                    {{ (!empty(old('file_category_id')) && old('file_category_id') == $fileCategory->id) ? 'selected': ''}}>
                    {{ $fileCategory->category_name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('file_category_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
      </div>
    </div>
</div>