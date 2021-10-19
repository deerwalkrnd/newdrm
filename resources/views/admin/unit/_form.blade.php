<div class="form-group">
    <label for="unit_name">Unit Name</label>
    <input type="text" class="form-control" id="unit_name" placeholder="Enter Unit Name" name="unit_name" value="{{ !empty(old('unit_name')) ? old('unit_name') : $unit->unit_name ?? '' }}">
    @error('unit_name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="organization_id">Organization</label>
    <select class="form-control" id="organization_id" name="organization_id">
        <option value="" disabled="disabled" selected="selected">-- Choose Organization --</option>
        @forelse($organizations as $organization)
        <option 
            value="{{ $organization->id}}" 
            {{ (!empty(old('organization_id')) && old('organization_id') == $organization->id) ? 'selected': ''}}
            {{ (isset($unit) && $unit->organization_id == $organization->id && empty(old('organization_id'))) ? 'selected' : '' }} 
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