<div class="form-group">
    <label for="category_name">Category Name*</label>
    <input type="text" class="form-control" id="category_name" placeholder="Enter Category Name" name="category_name" value="{{ !empty(old('category_name')) ? old('category_name') : $fileCategory->category_name ?? '' }}">
    @error('name')
        <p class="text-danger">{{ $message }}</p>
    @enderror
</div>
<!-- Category Name -->
