<div class="form-group">
    <label for="name">Leave Type</label>
    <input type="text" class="form-control" id="name" placeholder="Enter Leave Type" name="name" value="{{ !empty(old('name')) ? old('name') : $leaveType->name ?? '' }}">
    @error('name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
