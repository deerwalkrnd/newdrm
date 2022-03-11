@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/file-upload/create"><button class="btn btn-primary float-right">Upload File</button></a>
    <h3 class="text-success text-center">Uploaded File List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">File Category</th>
                    <th scope="col">Employee</th>                   
                    <th scope="col">Uploaded By</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($fileUploads as $fileUpload)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $fileUpload->fileCategory->category_name }}</td>
                    <td>{{ $fileUpload->employee->first_name.' '.substr($fileUpload->employee->middle_name,0,1).' '.$fileUpload->employee->last_name }}</td>
                    <td>{{ $fileUpload->uploaded_by }}</td>

                    <td>
                        <a href="/file-upload/download/{{ $fileUpload->id }}">Download</a> 
                        | 
                        <form action="/file-upload/{{ $fileUpload->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Uploaded File Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $fileUploads->links() }}
    </div>
</div>
@endsection