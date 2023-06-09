<!-- navigation bar start -->
<nav class="navbar navbar-expand-lg navigation navbar-dark">
        <div class="container-fluid">

            <a class="navbar-brand" href="/dashboard"><img src="/assets/images/logo.png" alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse navigation_mobile" id="navbarNavDropdown">
                <ul class="navbar-nav">

                    @if(Auth::user()->role->authority == 'hr')
                    <li class="nav-item dropdown navigation_item active">
                        <a class="nav-link navigation_link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-users icon"></i> HR Management</a>
                        <ul class="dropdown-menu hr_menu" aria-labelledby="navbarDropdownMenuLink">
                            <li>
                                <div class="table_container">
                                    <table class="hr_menu_table" width="100%">
                                        <tr>
                                            <td><a class="dropdown-item hr_item" href="#">
                                                    <div class="hr_menu_title text-uppercase">Employee</div>
                                                </a></td>
                                            <td><a class="dropdown-item hr_item" href="#">
                                                    <div class="hr_menu_title text-uppercase">Leave</div>
                                                </a></td>
                                            <td><a class="dropdown-item hr_item" href="#">
                                                    <div class="hr_menu_title text-uppercase">Settings</div>
                                                </a></td>
                                        </tr>

                                        <tr>
                                            <td class="hr_menu_content"><a href="/employee"><img class="img-fluid"
                                                        src="/assets/images/icons/employee.png">Employee
                                                    Detail</a></td>
                                            <td class="hr_menu_content"><a href="/leave-request/details"><img class="img-fluid"
                                                        src="/assets/images/icons/exit.png">Leave
                                                    Detail</a></td>
                                            <td class="hr_menu_content"><a href="/contact"><img class="img-fluid"
                                                        src="/assets/images/icons/contacts.png">Contacts</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="hr_menu_content"><a href="/employee/create"><img class="img-fluid"
                                                src="/assets/images/icons/add.png">Add
                                            Employee</a></td>
                                            <td class="hr_menu_content"><a href="/leave-request/approve"><img class="img-fluid"
                                                        src="/assets/images/icons/leave.png">Approve Leave</a></td>

                                            <td class="hr_menu_content"><a href="/designation"><img class="img-fluid"

                                                        src="/assets/images/icons/title.png">Designation</a></td>
                                        </tr>

                                        <tr>
                                            <td class="hr_menu_content"><a href="/employee/terminate"><img class="img-fluid"
                                                    src="/assets/images/icons/fired.png">Terminate
                                                Employee</a></td>
                                            <td class="hr_menu_content"><a href="/leave-balance-report"><img class="img-fluid"
                                                        src="/assets/images/icons/clipboard.png">Leave Balance
                                                    Report</a></td>
                                            <td class="hr_menu_content"><a href="/organization"><img class="img-fluid"
                                                        src="/assets/images/icons/organization.png">Organization</a></td>
                                        </tr>

                                        <tr>
                                            <td class="hr_menu_content"><a href="/file-upload"><img class="img-fluid"
                                                        src="/assets/images/icons/list.png">Upload Files
                                                </a></td>
                                            <td class="hr_menu_content">
                                            <!-- link doesnot work, it is just for information in frontend. redirection works through swal -->
                                            <a href="/info" id="confirmCalculateCarryOverLeave"><img class="img-fluid"
                                                        src="/assets/images/icons/logout.png" >Calculate Carry Over Leave
                                                </a>
                                                <!-- <a href="#"><img class="img-fluid"
                                                        src="/assets/images/icons/report.png">Monthly Leave
                                                    Report</a> -->
                                                </td>

                                            <td class="hr_menu_content"><a href="/unit"><img class="img-fluid"
                                                        src="/assets/images/icons/pie-chart.png">Unit</a></td>
                                        </tr>

                                        <tr>
                                            <td class="hr_menu_content"><a href="/shift"><img class="img-fluid"
                                                        src="/assets/images/icons/shifts.png">Shifts</a>
                                            </td>
                                            <td class="hr_menu_content"><a href="/employees-on-leave"><img class="img-fluid"
                                                        src="/assets/images/icons/leave-report.png">Employees on
                                                    Leave</a></td>
                                            <td class="hr_menu_content"><a href="/structure"><img class="img-fluid"
                                                        src="/assets/images/icons/management.png">Structure</a>
                                            </td>
                                        </tr>

                                        <tr>
                                             <td class="hr_menu_content"><a href="/change-password"><img class="img-fluid"
                                                        src="/assets/images/icons/skills.png">Change Password</a>
                                            </td>
                                            <td class="hr_menu_content"><a href="/leave-request/create/subordinate-leave"><img class="img-fluid"
                                                        src="/assets/images/icons/exit (1).png">Create
                                                    Subordinate Leave
                                                </a></td>
                                            <td class="hr_menu_content"><a href="/yearly-leaves"><img class="img-fluid"
                                                        src="/assets/images/icons/year.png">Yearly Leave
                                                    Details</a></td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <!-- <td class="hr_menu_content">
                                                <a href="/info"><img class="img-fluid"
                                                        src="/assets/images/icons/logout.png">Calculate Carry Over Leave
                                                </a>
                                            </td> -->
                                            <td class="hr_menu_content">
                                                <a href="/leave-request/forced"><img class="img-fluid"
                                                        src="/assets/images/icons/logout.png">Forced Leave
                                                </a>
                                            </td>
                                            <td class="hr_menu_content"><a href="/serviceType"><img class="img-fluid"
                                                        src="/assets/images/icons/service.png">Service
                                                    Type</a></td>
                                        </tr>

                                        <tr>
                                            <!-- <td class="hr_menu_content"><a href="#"><img class="img-fluid"
                                                        src="/assets/images/icons/padlock.png">Reset
                                                    Password</a></td> -->
                                                    <td></td>
                                            <td><a class="dropdown-item hr_item" href="#">
                                                    <div class="hr_menu_title text-uppercase">punch in/ out</div>
                                                </a></td>
                                            </a></td>
                                            <td class="hr_menu_content"><a href="/leaveType"><img class="img-fluid"
                                                        src="/assets/images/icons/logout (1).png">Leave
                                                    Type</a></td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td class="hr_menu_content"><a href="/punch-in-detail"><img class="img-fluid"
                                                        src="/assets/images/icons/calendar.png">Date/
                                                    Employee Specific
                                                </a></td>
                                            <td class="hr_menu_content"><a href="/manager"><img class="img-fluid"
                                                        src="/assets/images/icons/manager.png">Manager
                                                    Setting</a></td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td class="hr_menu_content"><a href="/no-punch-in-leave"><img class="img-fluid"
                                                        src="/assets/images/icons/search.png">No
                                                    Punch In
                                                    No Leave Report
                                                </a></td>
                                            <td class="hr_menu_content"><a href="/department"><img class="img-fluid"
                                                        src="/assets/images/icons/list.png">Department</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td class="hr_menu_content"><a href="/late-missed-punch"><img class="img-fluid"
                                                        src="/assets/images/icons/hurry.png">Late Punch In/
                                                      Missed Punch Out Report</a></td>
                                           <td class="hr_menu_content"><a href="/holiday"><img class="img-fluid"
                                                        src="/assets/images/icons/holiday.png">Holidays</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <!-- link doesnot work, it is just for information in frontend. redirection works through swal -->
                                            <td class="hr_menu_content"><a href="/force-punch-out" id="confirmForcePunchOut"><img class="img-fluid"
                                                src="/assets/images/icons/attendance.png">Force Punch Out
                                            </a></td>
                                            <td class="hr_menu_content"><a href="/mail"><img class="img-fluid"
                                                src="/assets/images/icons/mail.png">Mail Setting
                                            </a></td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="hr_menu_content"><a href="/time"><img class="img-fluid"
                                                src="/assets/images/icons/time.png">Time Setting
                                            </a></td>
                                        </tr>
                                         <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="hr_menu_content"><a href="/file-category"><img class="img-fluid"
                                                        src="/assets/images/icons/list.png">File Category Setting</a>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </li>
                        </ul>
                    </li>
                    @endif

                    @if(Auth::user()->role->authority == 'manager')
                    <li class="nav-item navigation_item">
                        <a class="nav-link navigation_link" href="/leave-request/create/subordinate-leave"><i class="fas fa-sign-out-alt icon"></i>
                            Create Sub Ordinate Leave</a>
                    </li>
                    @endif

                    @if(Auth::user()->role->authority == 'manager')
                    <li class="nav-item navigation_item">
                        <a class="nav-link navigation_link" href="/leave-request/show/subordinate-leave"><i class="fas"></i>
                            Show SubOrdinate Leave</a>
                    </li>
                    @endif
                    <li class="nav-item navigation_item">
                        <a class="nav-link navigation_link" href="/my-file-upload"><i class="fas fa-file-alt icon"></i>
                            My Files</a>
                    </li>
                    <li class="nav-item navigation_item">
                        <a class="nav-link navigation_link" href="/my-holiday"><i class="fas fa-sleigh icon"></i>
                            My Holiday</a>
                    </li>
                    <li class="nav-item navigation_item">
                        <a class="nav-link navigation_link" href="/contact"><i class="fas fa-address-book icon"></i>
                            Contacts</a>
                    </li>
                    <li class="nav-item navigation_item">
                        <a class="nav-link navigation_link" href="/change-password"><i class="fas fa-key"></i>
                            Change Password</a>
                    </li>
                    <!-- for mobile -->
                    <div class="nav_mobile">
                        <li class="nav-item navigation_item">
                            <span class="nav-link navigation_link">
                                Welcome: {{ \Auth::user()->employee->first_name." ".\Auth::user()->employee->last_name }}</a>
                        </li>
                        <li class="nav-item navigation_item">
                            <a class="nav-link navigation_link" href="javascript:{}" onclick="document.getElementById('logout_form').submit();"><i class="fas fa-power-off"></i>
                                Logout</a>
                        </li>
                    </div>


                    <!-- not for mobile -->
                    <li class="nav_right">
                        <span>Welcome: {{ \Auth::user()->employee->first_name." ".\Auth::user()->employee->last_name }}</span>
                        
                        <!-- logout -->
                        <span><a href="javascript:{}" onclick="document.getElementById('logout_form').submit();"><i class="fas fa-power-off"></i> Logout</a></span>
                        <form action="/logout" method="POST" id="logout_form">@csrf</form>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <!-- navigation bar end -->