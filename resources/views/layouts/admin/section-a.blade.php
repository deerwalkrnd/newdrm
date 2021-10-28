@include('layouts.admin.sidebar')

<div class="list-group list-group-flush" id="sidenav">
    <a href="#" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light"><i class="fas fa-home fa-lg nav-icon mr-1"></i> <span class="my-auto">Dashboard</span></a>
    <a href="/designation" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light {{ Request::is('designation') ? 'active' : '' }}"><i class="fas fa-book fa-lg nav-icon mr-2"></i> <span class="my-auto">Designation</span></a>
    <a href="/organization" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light {{ Request::is('organization') ? 'active' : '' }}"><i class="fas fa-book fa-lg nav-icon mr-2"></i> <span class="my-auto">Organization</span></a>
    <a href="/unit" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light {{ Request::is('unit') ? 'active' : '' }}"><i class="fas fa-book fa-lg nav-icon mr-2"></i> <span class="my-auto">Unit</span></a>
    <a href="/leaveType" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light {{ Request::is('leaveType') ? 'active' : '' }}"><i class="fas fa-book fa-lg nav-icon mr-2"></i> <span class="my-auto">Leave Type</span></a>
    <a href="/employee" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light {{ Request::is('leaveType') ? 'active' : '' }}"><i class="fas fa-book fa-lg nav-icon mr-2"></i> <span class="my-auto">Employee</span></a>
    <a href="/serviceType" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light {{ Request::is('serviceType') ? 'active' : '' }}"><i class="fas fa-book fa-lg nav-icon mr-2"></i> <span class="my-auto">Service Type</span></a>
    <a href="/manager" class="text-white list-group-item list-group-item-action bg-dark side-list border-bottom border-light {{ Request::is('manager') ? 'active' : '' }}"><i class="fas fa-book fa-lg nav-icon mr-2"></i> <span class="my-auto">Manager</span></a>
</div>