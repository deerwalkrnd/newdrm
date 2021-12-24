@extends('layouts.hr.app')

@section('title','Designation')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Designation List", "url" => "/designation/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
        <th scope="col" class="pl-4">S.N</th>
        <th scope="col">Job Title</th>
        <th scope="col">Job Description</th>
        <th scope="col">Action</th>
    </tr>
      @forelse($designations as $designation)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $designation->job_title_name }}</td>
            <td>{{ $designation->job_description }}</td>
            <td class="text-center">
                <a href="/designation/edit/{{ $designation->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/designation/{{ $designation->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="border-0" type="submit" class="delete"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Designation Found</th>
        </tr>
        @endforelse
</table>
{{ $designations->links() }}

@include('layouts.basic.tableFoot')
@endsection