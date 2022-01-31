@extends('layouts.hr.app')

@section('title','Structure')

@section('content')
<ul>
    @foreach($organizations as $organization)
        <li style="list-style:none;"><i class="fas fa-folder-open fa-2x" id="head{{ $loop->iteration }}" data-unit="body{{ $loop->iteration }}" onclick="toggleDisplay(this.id)" style="color:#3d6f95b5;"></i> <span style="font-size:38px;">{{$organization->name,$organization->code}}</span></li>
        <ul id="body{{ $loop->iteration }}">
            @foreach($organization->unit as $unit)
                <li style="list-style:none;"><i class="fas fa-folder fa-lg" style="color:#3d6f95b5;"></i> <span style="font-size:24px;">{{$unit->unit_name}}</span></li>
                <ul>
                @foreach($unit->topEmployees as $manager)
                    <li>{{ $manager }}</li>
                    @while($manager->workers != NULL)
                        <ul>
                        @foreach($manager->workers as $worker)
                            <li>{{ $worker }}</li>
                            <!-- <ul>
                            @foreach($worker->workers as $worker1)
                                <li>{{ $worker1 }}</li>                               
                            @endforeach
                            </ul>                                -->
                        @endforeach
                        </ul>
                    @endwhile
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
        console.log($('#'+id))
        if($('#'+id).hasClass('fa-folder-open'))
        {
            console.log("h1",id);
            $('#'+id).removeClass('fa-folder-open')
            $('#'+id).addClass('fa-folder')
        }else{
            console.log("h2",id);

            $('#'+id).addClass('fa-folder-open')
            $('#'+id).removeClass('fa-folder')
        }
    }
</script>
@endsection