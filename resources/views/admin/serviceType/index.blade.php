@extends('layouts.admin.app')

@section('content')
<div class="my-table">
    <a href="/serviceType/create"><button class="btn btn-primary float-right">Add Service Type</button></a>
    <h3 class="text-success text-center">Service Type List</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="pl-4">S.N</th>
                    <th scope="col">Service Type Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($serviceTypes as $serviceType)
                <tr>
                    <th scope="row" class="pl-4">{{ $loop->iteration }}</th>
                    <td>{{ $serviceType->service_type_name }}</td>
                    <td>
                        <a href="/serviceType/edit/{{ $serviceType->id }}">Edit</a> 
                        |
                        <form action="/serviceType/{{ $serviceType->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan=5 class="text-center">No Service Type Found</th>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection