@extends('layouts.hr.app')

@section('title','Yearly Leave')

@section('content')



@include('layouts.basic.tableHead',["table_title" => "Yearly Leave List", "url" => "/yearly-leaves/create"])


<div class="d-flex justify-content-between flex-row">
    <div class="w-25">
        <label for="year">Year: </label>
        <select class="form-control" name="year" onchange="search()" id="year">
            <option value="" disabled>- Choose -</option>
            @for($i=2011; $i<= date('Y'); $i++)
                <option value="{{$i}}" {{ (request()->get('y')) ? (request()->get('y')==$i ? 'selected':'') :($i == date('Y') ? 'selected':'') }}>{{ $i }}</option>
            @endfor
        </select>
        <!-- <input type="year" name="year" id="year" onchange="search()" value="{{ request()->get('y') ?? request()->get('y') }}" > -->
    </div> 
    <!-- <div >
        <button class="btn border-0 text-white" onclick="reset()" style="background-color:#0f5288">Reset</button>
    </div> -->
</div>
<br>
<table class="unit_table mx-auto drmDataTable">
    <thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Leave Type</th>
            <th scope="col">Unit</th>
            <th scope="col">Days</th>
            <th scope="col">Status</th>
            <th scope="col">Leave Year</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    @forelse($yearlyLeaves as $yearlyLeave)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $yearlyLeave->leaveType->name}}</td>
            @if($yearlyLeave->unit)
                <td>{{ $yearlyLeave->unit->unit_name }}</td>
            @else
                <td>All</td>
            @endif
            <td>{{ $yearlyLeave->days }}</td>
            <td>{{ $yearlyLeave->status }}</td>
            <td>{{ $yearlyLeave->year }}</td>
            <td class="text-center">
                <a href="/yearly-leaves/edit/{{ $yearlyLeave->id }}"><i class="far fa-edit"></i></a> 
                |
                <form action="/yearly-leaves/{{ $yearlyLeave->id }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete border-0 action"><i class="fas fa-trash-alt action"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <th colspan=7 class="text-center text-dark">No Yearly Leave Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $yearlyLeaves->links() --}}


@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
    //Search by year
    function search(){
        let year = $('#year').val();
        if(year)
            $(location).attr('href','/yearly-leaves?y='+year);
    }

    function reset(){
        $(location).attr('href','/yearly-leaves');
    }

</script>
@endsection