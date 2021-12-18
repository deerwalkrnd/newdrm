@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/file-category/create"><button class="btn btn-primary float-right">Add File Category</button></a>
    <h3 class="text-success text-center">File Category List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Category Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($fileCategories as $fileCategory)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $fileCategory->category_name }}</td>
                    <td>{{ $fileCategory->status }}</td>
                    <td>
                        <a href="/file-category/edit/{{ $fileCategory->id }}">Edit</a> 
                        | 
                        <form action="/file-category/{{ $fileCategory->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Holiday Created</th>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $fileCategories->links() }}
    </div>
</div>
@endsection