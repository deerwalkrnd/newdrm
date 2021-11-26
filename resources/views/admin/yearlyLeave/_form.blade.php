<div class="form-group">
    <label for="organization_id">Organization</label>
    <select class="form-control" id="organization_id" name="organization_id">
        <option value="" disabled="disabled" selected="selected">-- Choose Organization --</option>
        @forelse($organizations as $organization)
        <option 
            value="{{ $organization->id}}" 
            {{ (!empty(old('organization_id')) && old('organization_id') == $organization->id) ? 'selected': ''}}
            {{ (isset($yearlyLeaves) && $yearlyLeaves->organization_id == $organization->id && empty(old('organization_id'))) ? 'selected' : '' }} 
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
<!-- organization id -->

<div class="form-group">
    <label for="leave_type_id">Leave Type</label>
    <select class="form-control" id="leave_type_id" name="leave_type_id">
        <option value="" disabled="disabled" selected="selected">-- Choose Leave Type --</option>
        @forelse($leaveTypes as $leaveType)
        <option 
            value="{{ $leaveType->id}}" 
            {{ (!empty(old('leave_type_id')) && old('leave_type_id') == $leaveType->id) ? 'selected': ''}}
            {{ (isset($yearlyLeaves) && $yearlyLeaves->leave_type_id == $yearlyLeaves->id && empty(old('leave_type_id'))) ? 'selected' : '' }} 
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
<!-- leave type id -->

<div class="form-group">
    <label for="days">Leave Days*</label>
    <input type="text" class="form-control" id="days" placeholder="Enter leave days" name="days" value="{{ !empty(old('days')) ? old('days') : $yearlyLeaves->days ?? '' }}">  
    @error('days')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- leave days status -->

<div class="form-group">
    <label for="status">Status*</label>
    <select class="form-control" id="status" name="status">
        <option value="" disabled="disabled" selected="selected">-- Choose Status --</option>
        <option 
            value="active" selected
            {{ (!empty(old('status')) && old('status') == 'active') ? 'selected': ''}}
            {{ (isset($yearlyLeaves) && $yearlyLeaves->status == 'active' && empty(old('status'))) ? 'selected' : '' }} 
            >
            active
        </option>
        <option 
            value="disabled" 
            {{ (!empty(old('status')) && old('status') == 'disabled') ? 'selected': ''}}
            {{ (isset($yearlyLeaves) && $yearlyLeaves->status == 'disabled' && empty(old('status'))) ? 'selected' : '' }} 
            >
            disabled
        </option>
    </select>
    @error('status')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- paid-status -->



