<label for="employee_id">Employee Name*</label>
<select class="employee-livesearch form-control p-3 mb-2" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --">
    @if(!empty(old('employee_id')))
        <option value="{{ old('employee_id') }}" selected="selected">{{ old('employee_id') }}</option>
    @elseif(isset($leaveRequest) && !empty($leaveRequest->employee))
        <option value="{{ $leaveRequest->employee->id }}" selected="selected">{{ $leaveRequest->employee->first_name.' '.substr($leaveRequest->employee->middle_name,0).' '.$leaveRequest->employee->last_name }}</option>
    @endif
</select>
@error('employee_id')
    <p class="text-danger">{{ $message }}</p>
@enderror
<br><br>
<!-- choose employee -->
@include('admin.leaveRequest._form')