@extends('layouts.hr.app')

@section('title','File Category')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "File Category List", "url" => "/file-category/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Category Name</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($fileCategories as $fileCategory)
        <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td>{{ $fileCategory->category_name }}</td>
        <td>{{ $fileCategory->status }}</td>
        <td class="text-center">
            <a href="/file-category/edit/{{ $fileCategory->id }}"><i class="far fa-edit"></i></a> 
            | 
            <form action="/file-category/{{ $fileCategory->id }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button  class="border-0" type="submit" class="delete"><i class="fas fa-trash-alt action"></i></button>
            </form>
        </td>
        
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No File Category Created</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $fileCategories->links() --}}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection