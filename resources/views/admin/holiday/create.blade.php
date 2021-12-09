<div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" placeholder="Enter Holiday Name" name="name" value="{{ !empty(old('name')) ? old('name') : $holiday->name ?? '' }}">
    @error('name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="date">Date</label>
    <input type="text" class="form-control" id="date" placeholder="Enter Holiday date" date="date" value="{{ !empty(old('date')) ? old('date') : $holiday->date ?? '' }}">
    @error('date')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <div class="form-check form-check-inline">
        <input type="hidden" name="female_only" value="0">
        <input class="form-check-input" type="checkbox" name="female_only" id="female_only" value="1" 
        {{ (isset($holiday) && $holiday->female_only == '1') ? 'checked':'' }}
        {{ old('female_only') == '1' ? 'checked':'' }}>
        <label for="female_only">Female Only</label>
    </div>
    @error('female_only')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

