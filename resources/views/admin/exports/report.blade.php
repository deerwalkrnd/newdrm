<table>
    <thead>
        <tr>
            <th>Name</th>
            @foreach ($dates as $date)
                <th>{{ $date->format('Y-m-d') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($employees as $employee)
            <tr>
                <td>{{ $employee->first_name . ' ' . substr($employee->middle_name, 0, 1) . ' ' . $employee->last_name }}
                </td>
                @foreach ($attendanceStatuses[$employee->id] as $attendance)
                    <td>{!! $attendance !!}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($dates) + 1 }}">No records found</td>
            </tr>
        @endforelse
    </tbody>
</table>
