<div class="form-group">
    <label for="name">Organization Name</label>
    <input type="text" class="form-control" id="name" placeholder="Enter Organization Name" name="name" value="{{ !empty(old('name')) ? old('name') : $organization->name ?? '' }}">
    @error('name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>

<div class="form-group">
    <label for="code">Organization Code</label>
    <input type="text" class="form-control" id="code" placeholder="Enter Organization Code" name="code" value="{{ !empty(old('code')) ? old('code') : $organization->code ?? '' }}">
    @error('code')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>