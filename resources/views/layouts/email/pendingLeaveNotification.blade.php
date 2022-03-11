<div>
    <p>Dear {{ $employee }}</p>
    <p>This is gentle remainder that your leave request has not been approved yet. Please contact your manager for further detail.</p>
    <p><a href="{{ url('/leave-request/approve') }}">Click here</a> to take action.</p>
    <p>Regards,</p>
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