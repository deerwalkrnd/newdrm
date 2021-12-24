<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label"  for="name">Shift Type</label>
            <input type="text" class="form-control" id="name" placeholder="Enter Job Title" name="name" value="{{ !empty(old('name')) ? old('name') : $shift->name ?? '' }}">
            @error('name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <div class="form-check form-check-inline">
                <input type="hidden" name="time_required" value="0">
                <input class="form-check-input" type="checkbox" name="time_required" id="time_required" value="1" 
                {{ (isset($shift) && $shift->time_required == '1') ? 'checked':'' }}
                {{ old('time_required') == '1' ? 'checked':'' }}>
                <label class="form-label" for="time_required">Required Start/End Time</label>
            </div>
            @error('time_required')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
