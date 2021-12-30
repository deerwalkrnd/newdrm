<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="name">Contact Name</label>
            <input type="text" class="form-control" id="name" placeholder="Enter Contact Name" name="name" value="{{ !empty(old('name')) ? old('name') : $contact->name ?? '' }}">
            @error('name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="number">Contact Number</label>
            <input type="text" class="form-control" id="number" placeholder="Enter Contact Number" name="number" value="{{ !empty(old('number')) ? old('number') : $contact->number ?? '' }}">
            @error('number')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>