<div class="form-group">
    <label for="leave_type_id">Leave Type*</label>
    <select class="form-control" id="leave_type_id" name="leave_type_id">
        <option value="" disabled="disabled" selected="selected">-- Choose Leave Type --</option>
        @forelse($leaveTypes as $leaveType)
        <option 
            value="{{ $leaveType->id}}" 
            {{ (!empty(old('leave_type_id')) && old('leave_type_id') == $leaveType->id) ? 'selected': ''}}
            >
            {{ $leaveType->name }}
        </option>
        @empty
        <!-- no options -->
        @endforelse
    </select>
    @error('leave_type_id')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="half_leave" id="half_leave" value="1" 
        {{ (isset($leaveRequest) && $leaveRequest->half_leave == '1') ? 'checked':'' }}
        {{ old('half_leave') == '1' ? 'checked':'' }}>
        <label for="half_leave">Half Leave</label>
    </div>
    @error('half_leave')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="days">Leave Days</label>
    <input type="number" class="form-control" id="days" placeholder="Enter Leave Days" name="days" value="{{ !empty(old('days')) ? old('days') : $leaveRequest->days ?? '' }}">
    @error('days')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="start_date">Start Date</label>
    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ !empty(old('start_date')) ? old('start_date') : $leaveRequest->start_date ?? '' }}">
    @error('start_date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="end_date">Start Date</label>
    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ !empty(old('end_date')) ? old('end_date') : $leaveRequest->end_date ?? '' }}">
    @error('end_date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="reason">Reason</label>
    <input type="text" class="form-control" id="reason" placeholder="Enter Reason" name="reason" value="{{ !empty(old('reason')) ? old('reason') : $leaveRequest->reason ?? '' }}">
    @error('reason')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
