<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="name">Department Name*</label>
            <input type="text" class="form-control" id="name" placeholder="Enter Department Name" name="name" value="{{ !empty(old('name')) ? old('name') : $department->name ?? '' }}">
            @error('name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- Deparment Name -->
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
                    {{ (isset($department) && $department->unit_id == $unit->id && empty(old('unit_id'))) ? 'selected' : '' }} 
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