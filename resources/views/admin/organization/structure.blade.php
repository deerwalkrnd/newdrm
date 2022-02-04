@extends('layouts.hr.app')

@section('title','Structure')

@section('content')
<ul>
    @foreach($organizations as $organization)
        <li style="list-style:none;"><i class="fas fa-folder fa-2x" id="head{{ $loop->iteration }}" data-unit="body{{ $loop->iteration }}" onclick="toggleDisplay(this.id)" style="color:#3d6f95b5;"></i> <span style="font-size:38px;">{{$organization->name,$organization->code}}</span></li>
        <ul id="body{{ $loop->iteration }}" style="display:none;">
            @foreach($organization->unit as $unit)
                <li style="list-style:none;"><i class="fas fa-folder fa-lg" id="unit{{ $unit->id }}" data-unit="unitBody{{ $unit->id }}" onclick="toggleDisplay(this.id)" style="color:#3d6f95b5;"></i> <span style="font-size:24px;">{{$unit->unit_name}}</span></li>
                <ul id="unitBody{{ $loop->iteration }}" style="display:none;">
                @foreach($unit->topEmployees as $manager)
                    @if($manager->contract_status=='active')
                        <li style="list-style:none;"><i class="fas fa-user-tie"></i> {{ $manager->first_name." ".$manager->middle_name." ".$manager->last_name }}</li>
                    @endif
                        <ul>
                        @foreach($manager->workers as $worker)
                            @if($worker->contract_status=='active')
                                <li style="list-style:none;"><i class="fas fa-user"></i> {{ $worker->first_name." ".$worker->middle_name." ".$worker->last_name }}</li>
                            @endif
                            <ul>
                            @foreach($worker->workers as $worker1)
                                @if($worker1->contract_status=='active')
                                    <li style="list-style:none;"><i class="far fa-user"></i> {{ $worker1->first_name." ".$worker1->middle_name." ".$worker1->last_name }}</li>                               
                                @endif
                            @endforeach
                            </ul>                               
                        @endforeach
                        </ul>
                @endforeach
                </ul>
            @endforeach
        </ul>
    @endforeach
</ul>
@endsection


@section('scripts')
<script>
    function toggleDisplay(id)
    {
        unitId = $('#'+id).data('unit');
        $('#'+unitId).toggle();

        if($('#'+id).hasClass('fa-folder-open'))
        {
            $('#'+id).removeClass('fa-folder-open')
            $('#'+id).addClass('fa-folder')
        }else{
            $('#'+id).addClass('fa-folder-open')
            $('#'+id).removeClass('fa-folder')
        }
    }
</script>
@endsection