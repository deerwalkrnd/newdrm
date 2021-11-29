<div class="form-group">
    <label for="name">Leave Type</label>
    <input type="text" class="form-control" id="name" placeholder="Enter Leave Type" name="name" value="{{ !empty(old('name')) ? old('name') : $leaveType->name ?? '' }}">
    @error('name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="gender">Gender*</label>
    <br>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender2" value="All" 
        {{ (isset($leaveType) && $leaveType->gender == 'All') ? 'checked':'' }}
        {{ old('gender') == 'All' ? 'checked':'' }}>
        <label class="form-check-label" for="gender2">All</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender0" value="Female" 
        {{ (isset($leaveType) && $leaveType->gender == 'Female') ? 'checked':''}}
        {{ old('gender') == 'Female' ? 'checked':'' }}>
        <label class="form-check-label" for="gender0">Female</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender1" value="Male" 
        {{ (isset($leaveType) && $leaveType->gender == 'Male') ? 'checked':''}}
        {{ old('gender') == 'Male' ? 'checked':''}}>
        <label class="form-check-label" for="gender1">Male</label>
    </div>
    @error('gender')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
