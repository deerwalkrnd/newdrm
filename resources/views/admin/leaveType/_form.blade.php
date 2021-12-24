<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="name">Leave Type</label>
            <input type="text" class="form-control" id="name" placeholder="Enter Leave Type" name="name" value="{{ !empty(old('name')) ? old('name') : $leaveType->name ?? '' }}">
            @error('name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="gender">Gender*</label>
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
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="paid_unpaid">Paid/Unpaid*</label>
            <br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="paid_unpaid" id="paid" value="1" 
                {{ (isset($leaveType) && $leaveType->paid_unpaid == '1') ? 'checked':''}}
                {{ old('paid_unpaid') == '1' ? 'checked':'' }}>
                <label class="form-check-label" for="paid">Paid</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="paid_unpaid" id="unpaid" value="0" 
                {{ (isset($leaveType) && $leaveType->paid_unpaid == '0') ? 'checked':''}}
                {{ old('paid_unpaid') == '0' ? 'checked':''}}>
                <label class="form-check-label" for="unpaid">Unpaid</label>
            </div>
            @error('paid_unpaid')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <div class="form-check form-check-inline">
                <input type="hidden" name="include_holiday" value="0">
                <input class="form-check-input" type="checkbox" name="include_holiday" id="include_holiday" value="1" 
                {{ (isset($leaveType) && $leaveType->include_holiday == '1') ? 'checked':'' }}
                {{ old('include_holiday') == '1' ? 'checked':'' }}>
                <label class="form-label" class="form-check-label" for="female_only">Include Holiday Status</label>
            </div>
            @error('include_holiday')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>