<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="name">Organization Name</label>
            <input type="text" class="form-control" id="name" placeholder="Enter Organization Name" name="name" value="{{ !empty(old('name')) ? old('name') : $organization->name ?? '' }}">
            @error('name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="code">Organization Code</label>
            <input type="text" class="form-control" id="code" placeholder="Enter Organization Code" name="code" value="{{ !empty(old('code')) ? old('code') : $organization->code ?? '' }}">
            @error('code')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>