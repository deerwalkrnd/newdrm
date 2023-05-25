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
                    {{ (isset($holiday) && $holiday->unit_id == $unit->id && empty(old('unit_id'))) ? 'selected' : '' }} 
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
            <label class="form-label" for="name">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Enter Holiday Name" name="name" value="{{ !empty(old('name')) ? old('name') : $holiday->name ?? '' }}">
            @error('name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" placeholder="Enter Holiday date"  value="{{ !empty(old('date')) ? old('date') : $holiday->date ?? '' }}">
            @error('date')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <div class="form-check form-check-inline">
                <input type="hidden" name="female_only" value="0">
                <input class="form-check-input" type="checkbox" name="female_only" id="female_only" value="1" 
                {{ (isset($holiday) && $holiday->female_only == '1') ? 'checked':'' }}
                {{ old('female_only') == '1' ? 'checked':'' }}>
                <label class="form-label" class="form-check-label" for="female_only">Female Only</label>
            </div>
            @error('female_only')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <div class="form-check form-check-inline">
                <input type="hidden" name="festival_only" value="0">
                <input class="form-check-input" type="checkbox" name="festival_only" id="festival_only" value="1" 
                {{ (isset($holiday) && $holiday->festival_only == '1') ? 'checked' : '' }}
                {{ old('festival_only') == '1' ? 'checked' : '' }}>
                <label class="form-label" class="form-check-label" for="festival_only">Festival Only</label>
            </div>
            @error('festival_only')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="image">Image</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
    </div>
</div>
