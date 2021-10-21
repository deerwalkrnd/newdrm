<div class="form-group">
    <label for="service_type_name">Service Type Name</label>
    <input type="text" class="form-control" id="service_type_name" placeholder="Enter Job Title" name="service_type_name" value="{{ !empty(old('service_type_name')) ? old('service_type_name') : $serviceType->service_type_name ?? '' }}">
    @error('service_type_name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

