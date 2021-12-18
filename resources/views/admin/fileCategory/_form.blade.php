<div class="form-group">
    <label for="category_name">Category Name*</label>
    <input type="text" class="form-control" id="category_name" placeholder="Enter Category Name" name="category_name" value="{{ !empty(old('category_name')) ? old('category_name') : $fileCategory->category_name ?? '' }}">
    @error('name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- Category Name -->

<div class="form-group">
    <label for="status">Status*</label>
    <select class="form-control" id="status" name="status">
        <option 
            value="active" selected
            {{ (!empty(old('status')) && old('status') == 'active') ? 'selected': ''}}
            {{ (isset($fileCategory) && $fileCategory->status == 'active' && empty(old('status'))) ? 'selected' : '' }} 
            >
            active
        </option>
        <option 
            value="inactive" 
            {{ (!empty(old('status')) && old('status') == 'inactive') ? 'selected': ''}}
            {{ (isset($fileCategory) && $fileCategory->status == 'inactive' && empty(old('status'))) ? 'selected' : '' }} 
            >
            inactive
        </option>
    </select>
    @error('status')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- status -->