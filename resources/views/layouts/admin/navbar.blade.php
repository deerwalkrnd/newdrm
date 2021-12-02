<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand admin-logo-navbar" href="#"><img src="/assets/images/drmlogo.png" alt="DSS - DOKO" width="200" height="40"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Dashboard</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/designation">Designation</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/organization">Organization</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/unit">Unit</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/leaveType">Leave Type</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/yearly-leaves">Yearly Leave</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/employee">Employee</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/serviceType">Service Type</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/shift">Shift</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/manager">Manager</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/role">Role</a>
            </li>
        </ul>
        @if(session('punchIn') == 2)
        <form class="form-inline my-2 my-lg-0 mr-3" action="/punch-out" method="POST" onsubmit="return confirm('Do you want to punch-out?');">
            @csrf
            <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Punch Out</button>
        </form>
        @endif
        @if(session('punchIn') == 1)
        <form class="form-inline my-2 my-lg-0 mr-3" action="/punch-in" method="GET">
            <button class="btn btn-outline-info my-2 my-sm-0" type="submit">Punch In</button>
        </form>
        @endif
        <form class="form-inline my-2 my-lg-0" action="/logout" method="POST">
            @csrf
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Logout</button>
        </form>
    </div>
</nav>
