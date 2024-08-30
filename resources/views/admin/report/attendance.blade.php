@extends('layouts.hr.app')

@section('title', 'Employee')

@section('content')
    @include('layouts.basic.tableHead', ['table_title' => 'Attendance Report'])


    <div class="row">
        <div class="col-md-4">
            <form action="/attendancereport/search/" method="GET">
                @csrf
                <label class="form-label" for="employee_id">Employee:</label>
                <select class="employee-livesearch form-control "  name="e" id="employee_id"
                placeholder="-- Choose Employee --">
                <option value="">-- Choose Employee --</option>
                    @foreach ($employeeList as $employee)
                    <option value="{{$employee->id}}" {{$employee->id==$data->e ? "selected" : ""}}>
                        {{$employee->first_name. " " . $employee->middle_name . " ". $employee->last_name}}
                    </option>
                    @endforeach
                </select>
                        </div>
                        <div class="col-md-4">
                <label class="form-label" for="date">Start Date: </label>
                <input class="form-control p-2" type="date" name="sd" id="start_date" value="{{$data->sd}}">
                        </div>
                        <div class="col-md-4">
                <label class="form-label" for="date">End Date: </label>
                <input class="form-control p-2" type="date" name="ed" id="end_date" value="{{$data->ed}}">
                        </div>
                        <div class="col-md-4">
                <button class="btn border-0 text-white"
                    style="background-color:#1b880f;float:right;">Search</button>
                        </div>
            </form>
       
        <div class="col-md-4">
            <button class="btn border-0 text-white" id="downloadBtn" onclick="download()"
                style="background-color:#0f5288;float:right;">Download</button>
        </div>
    </div>

    <br>
    <table class="unit_table mx-auto drmDataTable">
        <thead>
            <tr class="table_title" style="background-color: #0f5288;">
                <th scope="col">Name</th>
                @foreach ($dates as $date)
                    <th scope="col" data-dt-order="disable">{{ $date->format('Y-m-d') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr>
                    <td>{{ $employee->first_name . ' ' . substr($employee->middle_name, 0, 1) . ' ' . $employee->last_name }}</td>

                    @foreach ($dates as $date)
                        @php
                            $attendance = $employee->attendances->first(function ($att) use ($date) {
                                return \Carbon\Carbon::parse($att->punch_in_time)->isSameDay($date);
                            });
                            
                        @endphp

                        @if ($attendance)
                            @php
                                $punchintime = \Carbon\Carbon::parse($attendance->punch_in_time)->format('H:i:s');
                                $punchouttime = \Carbon\Carbon::parse($attendance->punch_out_time)->format('H:i:s');       
                                // dd($punchintime,$punchouttime);
                                // dd($attendance);
                            @endphp
                            @if ($punchintime > '13:30:00' || $punchouttime < '13:30:00')
                                <td>P/A</td>
                            @else
                                <td>P</td>
                            @endif
                        @else
                            <td>A</td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 1 }}">No records found</td>
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
        });

        $('#employee_id').select2({});

        function download() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let employee_id = $('#employee_id').val();

            let queryParams =
                `?sd=${encodeURIComponent(startDate)}&ed=${encodeURIComponent(endDate)}&e=${encodeURIComponent(employee_id)}`;


            window.location.href = `/attendance/export${queryParams}`;
        }


        function reset() {
            $(location).attr('href', '/attendancereport');
            updateDownloadLink('');
        }

    </script>
@endsection
