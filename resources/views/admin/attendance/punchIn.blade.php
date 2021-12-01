@extends('layouts.admin.app')
@section('content')
<div class="my-form">
    <h3 class="text-success text-center">Punch In</h3>
    <form method="POST" action="/punch-in" enctype='multipart/form-data'>
        @csrf
        <input type="hidden" name="code" value="{{$code}}">
        <div class="row justify-content-center mt-4">
            <button type="submit" class="btn btn-primary p-3">Punch-In</button>
        </div>
        <br>
        <div class="form-group px-5">
            <input type="text" class="form-control" id="reason" placeholder="Enter Reason For Being Late *" name="reason" value="{{ !empty(old('reason')) ?? old('reason') }}">
            @error('reason')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <!-- reason -->
    </form>
</div>
@endsection