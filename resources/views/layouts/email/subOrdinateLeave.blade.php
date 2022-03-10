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
    <br>
    <div id="signature">
        --<br>
        HR | <span style="color:#0b5394;"><b>DRM SYSTEM</b></span><br>
        Deerwalk Education Group<br>
        Sifal, Kathmandu<br>
        Nepal<br>
        <a href="deerwalk.edu.np">deerwalk.edu.np</a>
        <br>
        <p style="color:888888; font-family: ui-monospace;">
            DISCLAIMER:<br>
            This is an automatically generated email - please do not reply to it. If you have any queries please contact HR.
        </p>
    </div>
</div>