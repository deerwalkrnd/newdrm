@extends('layouts.hr.app')

@section('title','Service Type')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Service Type List", "url" => "/serviceType/create"])
<table class="unit_table mx-auto">
    <tr class="table_title" style="background-color: #3573A3;">
       <th scope="col" class="ps-4">S.N</th>
       <th scope="col">Service Type Name</th>
       <th scope="col">Action</th>
    </tr>
   @forelse($serviceTypes as $serviceType)
    <tr>
        <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
        <td>{{ $serviceType->service_type_name }}</td>
        <td class="text-center">
            <a href="/serviceType/edit/{{ $serviceType->id }}"><i class="far fa-edit"></i></a> 
            |
            <form action="/serviceType/{{ $serviceType->id }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <th colspan=5 class="text-center text-dark">No Service Type Found</th>
    </tr>
    @endforelse
</table>
{{ $serviceTypes->links() }}

@include('layouts.basic.tableFoot')
@endsection