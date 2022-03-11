<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="job_title_name">Job Title*</label>
            <input type="text" class="form-control" id="job_title_name" placeholder="Enter Job Title" name="job_title_name" value="{{ !empty(old('job_title_name')) ? old('job_title_name') : $designation->job_title_name ?? '' }}">
            @error('job_title_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label class="form-label" for="job_description">Job Description*</label>
            <input type="text" class="form-control" id="job_description" placeholder="Enter Job Description" name="job_description" value="{{ !empty(old('job_description')) ? old('job_description') : $designation->job_description ?? '' }}">
            @error('job_description')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>