<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="unit_id">Unit</label>
            <select class="form-control" id="unit_id" name="unit_id">
                <option value="" disabled="disabled" selected="selected">-- Choose Unit --</option>
                <option value="" {{empty(old('unit_id')) ? 'selected':''}}>All</option>
                @forelse($units as $unit)
                <option 
                    value="{{ $unit->id}}" 
                    {{ (!empty(old('unit_id')) && old('unit_id') == $unit->id) ? 'selected': ''}}
                    {{ (isset($yearlyLeaves) && $yearlyLeaves->unit_id == $unit->id && empty(old('unit_id'))) ? 'selected' : '' }} 
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
    </div>
</div>
<!-- unit id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="leave_type_id">Leave Type</label>
            <select class="form-control" id="leave_type_id" name="leave_type_id">
                <option value="" disabled="disabled" selected="selected">-- Choose Leave Type --</option>
                @forelse($leaveTypes as $leaveType)
                <option 
                    value="{{ $leaveType->id }}" 
                    {{ (!empty(old('leave_type_id')) && old('leave_type_id') == $leaveType->id) ? 'selected': ''}}
                    {{ (isset($yearlyLeaves) && $yearlyLeaves->leave_type_id == $leaveType->id && empty(old('leave_type_id'))) ? 'selected' : '' }} 
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
    </div>
</div>
<!-- leave type id -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="days">Leave Days*</label>
            <input type="text" class="form-control" id="days" placeholder="Enter leave days" name="days" value="{{ !empty(old('days')) ? old('days') : $yearlyLeaves->days ?? '' }}">  
            @error('days')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- leave days status -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
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
    </div>
</div>
<!-- paid-status -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <!-- <label for="year">Year: </label> -->
        <!-- <select class="form-control" name="year"  id="year">
            @for($i=$thisYear-5; $i<= $thisYear+10; $i++)
                <option value="{{$i}}" {{ !empty(old('year')) ? old('year') : $yearlyLeaves->year ?? '' }}>{{ $i }}</option>
            @endfor
        </select> -->
            <label for="year">Year*</label>
            <input type="number" class="form-control" id="year" placeholder="Enter Leave Year" name="year" value="{{ !empty(old('year')) ? old('year') : $yearlyLeaves->year ?? '' }}" >  
            @error('year')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- leave year -->