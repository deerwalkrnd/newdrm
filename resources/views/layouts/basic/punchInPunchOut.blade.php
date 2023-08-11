<!-- check if valid ip else no action can be performed -->
@if(in_array($userIp, explode(',',env('IP'))))
    <!-- if state is 1 punch in is needed else if state is 2 punch out needed else no action available -->
    @if($state == 1)

        <!-- check if noPunchIn-noLeaveRecord Exists if no show punch-in option else hide -->
        @if(!$noPunchInNoLeaveRecordExists)

            <!-- check if on full-leave or any holiday else max time must be checked -->
            @if($max_punch_in_time == False) 
                <!-- include punch-in with reason -->
                @include('layouts.basic.punchIn',['reason' => 'required'])
            @else

                <!-- check if user is on time else ask for reason -->
                @if(time() <= strtotime($max_punch_in_time))
                    <!-- include punch-in without reason -->
                    @include('layouts.basic.punchIn')
                @else

                    <!-- check if late punch-in within 10days else include punchin with reason -->
                    @if($late_within_ten_days)
                        <p class="text-danger error-text">Multiple Late Punch-In Detected. Contact HR for punch-in.</p>
                    @else
                        <!-- include punch-in with reason -->
                        @include('layouts.basic.punchIn',['reason' => 'required'])
                    @endif
                @endif
            @endif
        @else
            <p class="text-danger error-text">No Punch In No Leave Record Exists. Contact HR to clear record.</p>
        @endif
    @elseif($state == 2)
        <!-- include punch out option -->
        @include('layouts.basic.punchOut')
    @else
        <p class="text-danger error-text">Punch Out Successful</p>
    @endif
@else
    <p class="text-danger error-text">Invalid IP Address Detected</p>
@endif