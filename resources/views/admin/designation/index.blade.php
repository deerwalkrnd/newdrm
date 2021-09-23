@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/designation/create"><button class="btn btn-primary float-right">Add Designation</button></a>
    <h3 class="text-success text-center">Designation List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Job Title</th>
                    <th scope="col">Job Description</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($designations as $designation)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $designation->job_title_name }}</td>
                    <td>{{ $designation->job_description }}</td>
                    <td>
                        <a href="/designation/edit/{{ $designation->id }}">Edit</a> 
                        | 
                        <form action="/designation/{{ $designation->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Designation Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $designations->links() }}
    </div>
</div>
@endsection