<div>
    <p>Dear {{$employee}},</p>
    <p>Subordinate leave has been requested on behalf of you by {{ $requested_by }}.</p>
    <p>Leave Details: </p>
    <p>
        <b>Manager: </b>{{ $manager }}<br>
        <b>Leave Type: </b>{{ $leave_type }}<br>
        <b>Start Date: </b>{{ $start_date }}<br>
        <b>End Date: </b>{{ $end_date }}<br>
        <b>Total Days: </b>{{ $days }}<br>
        <b>Leave Half: </b>{{ $leave_half }}<br>
    <p>
    <p>Thank you,</p>
</div>