<table>
    <thead>
        <tr>
            <th></th>
            @foreach ($dates as $date)
                <th colspan="3" style="text-align: center; font-weight: bold;border: 1px solid black;">{{ $date->format('Y-m-d') }}</th>
            @endforeach
        </tr>
        <tr>
            <th style="font-weight:bold; border: 1px solid black;">Name</th>
            @foreach ($dates as $date)
                <th style="font-weight:bold;border:1px solid black;">Status</th>
                <th style="font-weight:bold;border:1px solid black;">IN Time</th>
                <th style="font-weight:bold;border:1px solid black;">OUT Time</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($employees as $employee)
            <tr>
                <td style="border-right: 1px solid black;">{{ $employee->first_name . ' ' . substr($employee->middle_name, 0, 1) . ' ' . $employee->last_name }}
                </td>
                @foreach ($attendanceStatuses[$employee->id] as $date => $attendance)
                    <td>{{$attendance['status']}}</td>
                    <td>{{$attendance['punchin']}}</td>
                    <td style="border-right: 1px solid black;">{{$attendance['punchout']}}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($dates) + 1 }}">No records found</td>
            </tr>
        @endforelse
    </tbody>
</table>
