<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
           
            <label class="form-label" for="employee_id">Employee Name</label>
             @if(\Auth::user()->role->authority == 'hr')
            <select class="employee-livesearch form-control p-3 mb-2" name="employee_id" id="employee_id" data-placeholder="-- Choose Employee --"></select>           
            @else
            <select name="employee_id" class="form-control p-2" id="">
                <option value="{{$employee->id}}">{{ $employee->first_name.' '.$employee->middle_name.' '.$employee->last_name }}</option>
            </select>
            @endif
            
            @error('employee_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- select employee  -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="file">File Upload</label>
            <input type="file" class="form-control" id="file" placeholder="" name="file" >
            @error('file')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
<!-- file Upload -->

<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="file_category_id">File Category</label>
            <select class="form-control" id="file_category_id" name="file_category_id">
                <option value="" disabled="disabled" selected="selected">-- Choose File Category --</option>
                @forelse($fileCategories as $fileCategory)
                <option 
                    value="{{ $fileCategory->id}}" 
                    {{ (!empty(old('file_category_id')) && old('file_category_id') == $fileCategory->id) ? 'selected': ''}}>
                    {{ $fileCategory->category_name }}
                </option>
                @empty
                <!-- no options -->
                @endforelse
            </select>
            @error('file_category_id')
                <p class="text-danger">{{ $message }}</p>
            @enderror
      </div>
    </div>
</div>