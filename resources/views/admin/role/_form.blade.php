<div class="form-group">
    <label for="authority">Role Name</label>
    <input type="text" class="form-control" id="authority" placeholder="Enter Role Name" name="authority" value="{{ !empty(old('authority')) ? old('authority') : $role->authority ?? '' }}">
    @error('authority')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>