@extends('layouts.hr.app')

@section('title','Contact')

@section('content')
@if(Auth::user()->role->authority == 'hr')
    @include('layouts.basic.tableHead',["table_title" => "Contact List", "url" => "/contact/create"])
@else
    @include('layouts.basic.tableHead',["table_title" => "Contact List"])
@endif
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Contact Name</th>
            <th scope="col">Contact Number</th>
            @if(\Auth::user()->role->authority == 'hr')
            <th scope="col">Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $contact)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->number }}</td>
                @if(\Auth::user()->role->authority == 'hr')
                <td class="text-center">
                    <a href="/contact/edit/{{ $contact->id }}"><i class="far fa-edit"></i></a> 
                    | 
                    <form action="/contact/{{ $contact->id }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete action border-0"><i class="fas fa-trash-alt action"></i></button>
                    </form>
                </td>
                @endif

            </tr>
        @empty
            <tr>
                <th colspan=5 class="text-center text-dark">No Contact Found</th>
            </tr>
        @endforelse

        @foreach($employees as $employee)
            <tr>
                <th scope="row" class="ps-4 text-dark">{{ $loop->iteration + $contact->count() }}</th>
                <td class="white-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<img src='{{ ($employee->image_name != NULL) ? asset($employee->image_name) : '/assets/images/logop.jpg' }}' width='100%'  />">
                    {{ $employee->first_name.' '.$employee->middle_name.' '.$employee->last_name }}
                </td>
                <td>{{ $employee->mobile }}</td>
                @if(\Auth::user()->role->authority == 'hr')
                <td class="text-center">
                    <a href="/employee-contact/edit/{{ $employee->id }}"><i class="far fa-edit"></i></a> 
                </td>
                @endif

            </tr>
        @endforeach
    </tbody>
</table>

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        $('[data-bs-toggle="tooltip"]').tooltip();
        $('[data-bs-toggle="tooltip"]').on('inserted.bs.tooltip',function () {
            var thisClass = $(this).attr("class");
            $('.tooltip-inner').addClass(thisClass);
            $('.arrow').addClass(thisClass + "-arrow");
        });

        $('.drmDataTable').DataTable();        
    })
</script>
@endsection