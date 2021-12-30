@extends('layouts.hr.app')

@section('title','Contact')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Contact List", "url" => "/contact/create"])
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Contact Name</th>
            <th scope="col">Contact Number</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    @forelse($contacts as $contact)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $contact->name }}</td>
            <td>{{ $contact->number }}</td>
            <td class="text-center">
                <a href="/contact/edit/{{ $contact->id }}"><i class="far fa-edit"></i></a> 
                | 
                <form action="/contact/{{ $contact->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=5 class="text-center text-dark">No Contact Found</th>
        </tr>
        @endforelse
    </tbody>
</table>

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection