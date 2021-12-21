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
<!-- leave type -->
<hr>



<div class="form-group">
    <label for="year">Year*</label>
    <input type="number" class="form-control" id="year" placeholder="Enter Leave Year" name="year" value="{{ !empty(old('year')) ? old('year') : $leaveRequest->year ?? '' }}" >  
    @error('year')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- leave request year -->

<div class="form-group">
    <label for="half_leave">Leave Time*</label>
    <br>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="leave_time" id="leave_time0" value="full" 
        {{ (isset($leaveRequest) && $leaveRequest->leave_time == 'full') ? 'checked':''}}
        {{ old('leave_time') == 'full' ? 'checked':'' }}>
        <label class="form-check-label" for="leave_time0">Full Day</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="leave_time" id="leave_time1" value="first" 
        {{ (isset($leaveRequest) && $leaveRequest->leave_time == 'first') ? 'checked':''}}
        {{ old('leave_time') == 'first' ? 'checked':'' }}>
        <label class="form-check-label" for="leave_time1">First Half</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="leave_time" id="leave_time2" value="second" 
        {{ (isset($leaveRequest) && $leaveRequest->leave_time == 'second') ? 'checked':''}}
        {{ old('leave_time') == 'second' ? 'checked':'' }}>
        <label class="form-check-label" for="leave_time2">Second Half</label>
    </div>
    @error('half_leave')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<hr>
<div class="form-group">
    <label for="days">Leave Days*</label>
    <input type="number" class="form-control" id="days" placeholder="Enter Leave Days" name="days" value="{{ !empty(old('days')) ? old('days') : $leaveRequest->days ?? '' }}">
    @error('days')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="start_date">Start Date*</label>
    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ !empty(old('start_date')) ? old('start_date') : $leaveRequest->start_date ?? '' }}">
    @error('start_date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="end_date">End Date*</label>
    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ !empty(old('end_date')) ? old('end_date') : $leaveRequest->end_date ?? '' }}">
    @error('end_date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="reason">Reason*</label>
    <input type="text" class="form-control" id="reason" placeholder="Enter Reason" name="reason" value="{{ !empty(old('reason')) ? old('reason') : $leaveRequest->reason ?? '' }}">
    @error('reason')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
