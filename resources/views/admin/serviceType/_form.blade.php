<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="service_type_name">Service Type Name</label>
            <input type="text" class="form-control" id="service_type_name" placeholder="Enter Job Title" name="service_type_name" value="{{ !empty(old('service_type_name')) ? old('service_type_name') : $serviceType->service_type_name ?? '' }}">
            @error('service_type_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <div class="form-check form-check-inline">
                <input type="hidden" name="date_required" value="0">
                <input class="form-check-input" type="checkbox" name="date_required" id="date_required" value="1" 
                {{ (isset($serviceType) && $serviceType->date_required == '1') ? 'checked':'' }}
                {{ old('date_required') == '1' ? 'checked':'' }}>
                <label class="form-label" for="date_required">Required Start/End Date</label>
            </div>
            @error('date_required')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
