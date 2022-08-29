<div>
    <p>Hello All,</p>
    <p>This message is to inform you that the following team members of DSS are on leave today. According to DRM, no one is on leave from DWG & DWC.
    <div >
        <table border=1>
            <tr>
                <th>
                    <center>S.N.</center>
                </th>
                <th>Name of Employee</th>
                <th>Leave Type</th>
                <th>Unit</th>
            </tr>
            @forelse($leaveList as $onLeave)
            <tr>
                <td>
                    <center>{{ $loop->iteration }}</center>
                </td>
                <td>{{ $onLeave->employee->first_name." ".$onLeave->employee->last_name}}</td>
                <td>{{ $onLeave->leaveType->name }}</td>
                <td>{{ $onLeave->employee->unit->unit_name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <center>No Employees On Leave Today.</center>
                </td>
            </tr>
            @endforelse
        </table>
    </div>
   
    </p>
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