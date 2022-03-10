<div>
    <p>Dear {{$manager}},</p>
    <p>{{ $employee }} has applied for {{ $leave_type }} leave from {{ $start_date }} to {{ $end_date }}. Total of {{ $days }} {{ $leave_half }} leave.</p>
    <p><a href="{{ url('/leave-request/approve') }}">Click here</a> to take action.</p>
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