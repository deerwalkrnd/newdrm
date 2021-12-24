@extends('layouts.hr.app')

@section('title','File Category')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "File Category List", "url" => "/file-category/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
       <th scope="col" class="ps-4">S.N</th>
        <th scope="col">Category Name</th>
        <th scope="col">Status</th>
        <th scope="col">Action</th>
    </tr>
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

</table>
{{ $fileCategories->links() }}

@include('layouts.basic.tableFoot')
@endsection