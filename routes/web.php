<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\YearlyLeaveController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LeaveReportController;
use App\Http\Controllers\CarryOverLeaveController;
use App\Http\Controllers\FileCategoryController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\PunchInOutReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MailControlController;
use App\Http\Controllers\NoPunchInNoLeaveController;
use App\Http\Controllers\TimeController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});


// Route::middleware(['logged-in'])->group(function(){
//     // designation route
//     Route::get('/designation/create',[DesignationController::class, 'create']);
//     Route::post('/designation',[DesignationController::class, 'store']);
//     Route::get('/designation',[DesignationController::class, 'index']);
//     Route::get('/designation/edit/{id}',[DesignationController::class, 'edit']);
//     Route::put('/designation/{id}',[DesignationController::class, 'update']);
//     Route::delete('/designation/{id}',[DesignationController::class, 'destroy']);

//     // organization route
//     Route::get('/organization/create',[OrganizationController::class, 'create']);
//     Route::post('/organization',[OrganizationController::class, 'store']);
//     Route::get('/organization',[OrganizationController::class, 'index']);
//     Route::get('/organization/edit/{id}',[OrganizationController::class, 'edit']);
//     Route::put('/organization/{id}',[OrganizationController::class, 'update']);
//     Route::delete('/organization/{id}',[OrganizationController::class, 'destroy']);
//     Route::get('/structure',[OrganizationController::class, 'structure']);

//     // unit route
//     Route::get('/unit/create',[UnitController::class, 'create']);
//     Route::post('/unit',[UnitController::class, 'store']);
//     Route::get('/unit',[UnitController::class, 'index']);
//     Route::get('/unit/edit/{id}',[UnitController::class, 'edit']);
//     Route::put('/unit/{id}',[UnitController::class, 'update']);
//     Route::delete('/unit/{id}',[UnitController::class, 'destroy']);

//     // leave-type route
//     Route::get('/leaveType/create',[LeaveTypeController::class, 'create']);
//     Route::post('/leaveType',[LeaveTypeController::class, 'store']);
//     Route::get('/leaveType',[LeaveTypeController::class, 'index']);
//     Route::get('/leaveType/edit/{id}',[LeaveTypeController::class, 'edit']);
//     Route::put('/leaveType/{id}',[LeaveTypeController::class, 'update']);
//     Route::delete('/leaveType/{id}',[LeaveTypeController::class, 'destroy']);

//     // employee route
//     Route::get('/employee/create',[EmployeeController::class, 'create']);
//     Route::post('/employee',[EmployeeController::class, 'store']);
//     Route::get('/employee',[EmployeeController::class, 'index']);
//     Route::get('/employee/edit/{id}',[EmployeeController::class, 'edit']);
//     Route::put('/employee/{id}',[EmployeeController::class, 'update']);
//     Route::delete('/employee/{id}',[EmployeeController::class, 'destroy']);
//     Route::get('/employee/search',[EmployeeController::class, 'search']);
//     Route::get('/employee/terminate',[EmployeeController::class, 'terminated']);
//     Route::post('/employee/terminate',[EmployeeController::class, 'terminate']);
//     Route::get('/employee/profile/{id?}',[EmployeeController::class, 'profile']);

//     // service type route
//     Route::get('/serviceType/create',[ServiceTypeController::class, 'create']);
//     Route::post('/serviceType',[ServiceTypeController::class, 'store']);
//     Route::get('/serviceType',[ServiceTypeController::class, 'index']);
//     Route::get('/serviceType/edit/{id}',[ServiceTypeController::class, 'edit']);
//     Route::put('/serviceType/{id}',[ServiceTypeController::class, 'update']);
//     Route::delete('/serviceType/{id}',[ServiceTypeController::class, 'destroy']);

//     // shift route
//     Route::get('/shift/create',[ShiftController::class, 'create']);
//     Route::post('/shift',[ShiftController::class, 'store']);
//     Route::get('/shift',[ShiftController::class, 'index']);
//     Route::get('/shift/edit/{id}',[ShiftController::class, 'edit']);
//     Route::put('/shift/{id}',[ShiftController::class, 'update']);
//     Route::delete('/shift/{id}',[ShiftController::class, 'destroy']);

//     // manager route
//     Route::get('/manager/create',[ManagerController::class, 'create']);
//     Route::post('/manager',[ManagerController::class, 'store'])->middleware(['pre.process.manager.request']);
//     Route::get('/manager',[ManagerController::class, 'index']);
//     Route::get('/manager/edit/{id}',[ManagerController::class, 'edit']);
//     Route::put('/manager/{id}',[ManagerController::class, 'update'])->middleware(['pre.process.manager.request']);
//     Route::delete('/manager/{id}',[ManagerController::class, 'destroy']);

//     // role route
//     Route::get('/role/create',[RoleController::class, 'create']);
//     Route::post('/role',[RoleController::class, 'store']);
//     Route::get('/role',[RoleController::class, 'index']);
//     Route::get('/role/edit/{id}',[RoleController::class, 'edit']);
//     Route::put('/role/{id}',[RoleController::class, 'update']);
//     Route::delete('/role/{id}',[RoleController::class, 'destroy']);

//     // contact route
//     Route::get('/contact/create',[ContactController::class, 'create']);
//     Route::post('/contact',[ContactController::class, 'store']);
//     Route::get('/contact',[ContactController::class, 'index']);
//     Route::get('/contact/edit/{id}',[ContactController::class, 'edit']);
//     Route::put('/contact/{id}',[ContactController::class, 'update']);
//     Route::delete('/contact/{id}',[ContactController::class, 'destroy']);

//     // yearly leaves route
//     Route::get('/yearly-leaves/create',[YearlyLeaveController::class, 'create']);
//     Route::post('/yearly-leaves',[YearlyLeaveController::class, 'store']);
//     Route::get('/yearly-leaves',[YearlyLeaveController::class, 'index']);
//     Route::get('/yearly-leaves/edit/{id}',[YearlyLeaveController::class, 'edit']);
//     Route::put('/yearly-leaves/{id}',[YearlyLeaveController::class, 'update']);
//     Route::delete('/yearly-leaves/{id}',[YearlyLeaveController::class, 'destroy']);

//     // leave request routes
//     Route::get('/leave-request/create',[LeaveRequestController::class, 'create']);
//     Route::post('/leave-request',[LeaveRequestController::class, 'store']);
//     Route::get('/leave-request',[LeaveRequestController::class, 'index']);
//     //subordinate leaves
//     Route::get('/leave-request/create/subordinate-leave',[LeaveRequestController::class, 'createSubOrdinateLeave']);
//     Route::post('/leave-request/subordinate-leave',[LeaveRequestController::class, 'storeSubOrdinateLeave']);
//     //leave-details
//     Route::get('/leave-request/edit/{id}',[LeaveRequestController::class, 'edit']);
//     Route::put('/leave-request/{id}',[LeaveRequestController::class, 'update']);
//     //approve-reject-delete leaves
//     Route::put('/leave-request/accept/{id}',[LeaveRequestController::class, 'accept']);
//     Route::put('/leave-request/reject/{id}',[LeaveRequestController::class, 'reject']);
//     Route::delete('/leave-request/{id}',[LeaveRequestController::class, 'destroy']);
//     //leave-applications
//     Route::get('/leave-request/approve',[LeaveRequestController::class, 'approve']);
//     //my-leave details
//     Route::get('/leave-request/details',[LeaveRequestController::class, 'leaveDetail']);
    

//     Route::get('/punch-in',[AttendanceController::class, 'index']);
//     Route::post('/punch-in',[AttendanceController::class, 'punchIn']);
//     Route::post('/punch-out',[AttendanceController::class, 'punchOut']);
   
//     // holiday route
//     Route::get('/holiday/create',[HolidayController::class, 'create']);
//     Route::post('/holiday',[HolidayController::class, 'store']);
//     Route::get('/holiday',[HolidayController::class, 'index']);
//     Route::get('/my-holiday',[HolidayController::class, 'myHoliday']);
//     Route::get('/holiday/edit/{id}',[HolidayController::class, 'edit']);
//     Route::put('/holiday/{id}',[HolidayController::class, 'update']);
//     Route::delete('/holiday/{id}',[HolidayController::class, 'destroy']);

//     //Search District
//     Route::get('/district/search/{id?}',[SearchController::class, 'searchDistrict']);

//     //Leave Report
//     Route::get('/leave-balance-report',[LeaveReportController::class, 'leaveBalance']);
//     Route::get('/no-punch-in-leave',[LeaveReportController::class, 'noPunchInNoLeave']);

//     //Employees On Leave Report
//     Route::get('/employees-on-leave',[LeaveReportController::class, 'employeesOnLeave']);
    
//     //Carry Over Leave
//     Route::get('/info',[CarryOverLeaveController::class,'calculateCarryOverLeave']);

//     // fileCategory route
//     Route::get('/file-category/create',[FileCategoryController::class, 'create']);
//     Route::post('/file-category',[FileCategoryController::class, 'store']);
//     Route::get('/file-category',[FileCategoryController::class, 'index']);
//     Route::get('/file-category/edit/{id}',[FileCategoryController::class, 'edit']);
//     Route::put('/file-category/{id}',[FileCategoryController::class, 'update']);
//     Route::delete('/file-category/{id}',[FileCategoryController::class, 'destroy']);
   
//     // fileUpload route
//     Route::get('/file-upload/create',[FileUploadController::class, 'create']);
//     Route::post('/file-upload',[FileUploadController::class, 'store']);
//     Route::get('/file-upload',[FileUploadController::class, 'index']);
//     Route::get('/my-file-upload',[FileUploadController::class, 'myFileIndex']);
//     Route::get('/file-upload/download/{id}',[FileUploadController::class, 'download']);
//     Route::delete('/file-upload/{id}',[FileUploadController::class, 'destroy']);
 
//     // Send Mail Route
//     Route::get('send-mail',[SendMailController::class,'punchOutMail']);

//     //get punch in and out report
//     Route::get('/punch-in-detail',[PunchInOutReportController::class,'getPunchInOut']);
//     Route::get('/myPunchIn',[AttendanceController::class,'myPunchIn']);
//      //late-punch-in missed-punch-out
//     Route::get('/late-missed-punch',[PunchInOutReportController::class, 'latePunchInOut']);

//     Route::get('/dashboard',[DashboardController::class, 'index']);
//     Route::view('/form','admin.dashboard.form');
//     Route::view('/table','admin.dashboard.table');    
// });

// lowest level employee
Route::middleware(['logged-in'])->group(function(){
    //change-password
    Route::view('change-password','auth.changePassword');
    //dashboard
    Route::get('/dashboard',[DashboardController::class, 'index']);
    Route::view('/form','admin.dashboard.form');
    Route::view('/table','admin.dashboard.table');    

    //punch-in-out
    Route::get('/punch-in',[AttendanceController::class, 'index']);
    Route::post('/punch-in/{id?}',[AttendanceController::class, 'punchIn']);
    Route::post('/punch-out',[AttendanceController::class, 'punchOut']);
    Route::get('/myPunchIn',[AttendanceController::class,'myPunchIn']);
    Route::post('/force-punch-in/{id}',[AttendanceController::class, 'forcePunchIn']);

    Route::get('/test',[NoPunchInNoLeaveController::class, 'create']);
    Route::get('/test-remove',[NoPunchInNoLeaveController::class, 'remove']);
   
    //contact
    Route::get('/contact',[ContactController::class, 'index']);

    //leave-request
    Route::get('/leave-request/create',[LeaveRequestController::class, 'create']);
    Route::post('/leave-request',[LeaveRequestController::class, 'store']);
    Route::get('/leave-request',[LeaveRequestController::class, 'index']);
    Route::get('/leave-request/forced',[LeaveRequestController::class, 'getForcedLeave']);
    Route::delete('/leave-request/force/{id}',[LeaveRequestController::class, 'forceDestroy']);
    Route::delete('/leave-request/{id}',[LeaveRequestController::class, 'destroy']);
  
    //employee profile
    Route::get('/employee/profile/{id?}',[EmployeeController::class, 'profile']);
    //my-holiday
    Route::get('/my-holiday',[HolidayController::class, 'myHoliday']);

     // Send Mail Route
    Route::get('send-mail',[SendMailController::class,'punchOutMail']);

    // fileUpload route
    Route::get('/file-upload/create',[FileUploadController::class, 'create']);
    Route::post('/file-upload',[FileUploadController::class, 'store']);
    Route::get('/file-upload',[FileUploadController::class, 'index']);
    Route::get('/my-file-upload',[FileUploadController::class, 'myFileIndex']);
    Route::get('/file-upload/download/{id}',[FileUploadController::class, 'download']);
    Route::delete('/file-upload/{id}',[FileUploadController::class, 'destroy']);
});

// lowest level manager
Route::middleware(['manager'])->group(function(){
    //subordinate leaves
    Route::get('/leave-request/create/subordinate-leave',[LeaveRequestController::class, 'createSubOrdinateLeave']);
    Route::post('/leave-request/subordinate-leave',[LeaveRequestController::class, 'storeSubOrdinateLeave']);
    //approve-reject-delete leaves
    Route::put('/leave-request/accept/{id}',[LeaveRequestController::class, 'accept']);
    Route::put('/leave-request/reject/{id}',[LeaveRequestController::class, 'reject']);
    //leave-applications
    Route::get('/leave-request/approve',[LeaveRequestController::class, 'approve']);
    //employee-livesearch
    Route::get('/employee/search',[EmployeeController::class, 'search']);


});

// lowest level hr
Route::middleware(['hr'])->group(function(){
    //time setting
    Route::get('/time',[TimeController::class,'index']);
    Route::post('/time/{id}',[TimeController::class,'store']);

    //mailSetting
    Route::get('/mail',[MailControlController::class,'index']);
    Route::post('/mail/{id}',[MailControlController::class,'store']);


    //get punch in and out report
    Route::get('/punch-in-detail',[PunchInOutReportController::class,'getPunchInOut']);
    // late-punch-in missed-punch-out
    Route::get('/late-missed-punch',[PunchInOutReportController::class, 'latePunchInOut']);

    // department route
    Route::get('/department/create',[DepartmentController::class, 'create']);
    Route::post('/department',[DepartmentController::class, 'store']);
    Route::get('/department',[DepartmentController::class, 'index']);
    Route::get('/department/edit/{id}',[DepartmentController::class, 'edit']);
    Route::put('/department/{id}',[DepartmentController::class, 'update']);
    Route::delete('/department/{id}',[DepartmentController::class, 'destroy']);

    // designation route
    Route::get('/designation/create',[DesignationController::class, 'create']);
    Route::post('/designation',[DesignationController::class, 'store']);
    Route::get('/designation',[DesignationController::class, 'index']);
    Route::get('/designation/edit/{id}',[DesignationController::class, 'edit']);
    Route::put('/designation/{id}',[DesignationController::class, 'update']);
    Route::delete('/designation/{id}',[DesignationController::class, 'destroy']);

     // organization route
    Route::get('/organization/create',[OrganizationController::class, 'create']);
    Route::post('/organization',[OrganizationController::class, 'store']);
    Route::get('/organization',[OrganizationController::class, 'index']);
    Route::get('/organization/edit/{id}',[OrganizationController::class, 'edit']);
    Route::put('/organization/{id}',[OrganizationController::class, 'update']);
    Route::delete('/organization/{id}',[OrganizationController::class, 'destroy']);
    Route::get('/structure',[OrganizationController::class, 'structure']);

     // unit route
    Route::get('/unit/create',[UnitController::class, 'create']);
    Route::post('/unit',[UnitController::class, 'store']);
    Route::get('/unit',[UnitController::class, 'index']);
    Route::get('/unit/edit/{id}',[UnitController::class, 'edit']);
    Route::put('/unit/{id}',[UnitController::class, 'update']);
    Route::delete('/unit/{id}',[UnitController::class, 'destroy']);

    // leave-type route
    Route::get('/leaveType/create',[LeaveTypeController::class, 'create']);
    Route::post('/leaveType',[LeaveTypeController::class, 'store']);
    Route::get('/leaveType',[LeaveTypeController::class, 'index']);
    Route::get('/leaveType/edit/{id}',[LeaveTypeController::class, 'edit']);
    Route::put('/leaveType/{id}',[LeaveTypeController::class, 'update']);
    Route::delete('/leaveType/{id}',[LeaveTypeController::class, 'destroy']);

    // employee route
    Route::get('/employee/create',[EmployeeController::class, 'create']);
    Route::post('/employee',[EmployeeController::class, 'store']);
    Route::get('/employee',[EmployeeController::class, 'index']);
    Route::get('/employee/edit/{id}',[EmployeeController::class, 'edit']);
    Route::put('/employee/{id}',[EmployeeController::class, 'update']);
    Route::delete('/employee/{id}',[EmployeeController::class, 'destroy']);
    Route::get('/employee/terminate',[EmployeeController::class, 'terminated']);
    Route::post('/employee/terminate',[EmployeeController::class, 'terminate']);

    // service type route
    Route::get('/serviceType/create',[ServiceTypeController::class, 'create']);
    Route::post('/serviceType',[ServiceTypeController::class, 'store']);
    Route::get('/serviceType',[ServiceTypeController::class, 'index']);
    Route::get('/serviceType/edit/{id}',[ServiceTypeController::class, 'edit']);
    Route::put('/serviceType/{id}',[ServiceTypeController::class, 'update']);
    Route::delete('/serviceType/{id}',[ServiceTypeController::class, 'destroy']);

    // shift route
    Route::get('/shift/create',[ShiftController::class, 'create']);
    Route::post('/shift',[ShiftController::class, 'store']);
    Route::get('/shift',[ShiftController::class, 'index']);
    Route::get('/shift/edit/{id}',[ShiftController::class, 'edit']);
    Route::put('/shift/{id}',[ShiftController::class, 'update']);
    Route::delete('/shift/{id}',[ShiftController::class, 'destroy']);

    // manager route
    Route::get('/manager/create',[ManagerController::class, 'create']);
    Route::post('/manager',[ManagerController::class, 'store'])->middleware(['pre.process.manager.request']);
    Route::get('/manager',[ManagerController::class, 'index']);
    Route::get('/manager/edit/{id}',[ManagerController::class, 'edit']);
    Route::put('/manager/{id}',[ManagerController::class, 'update'])->middleware(['pre.process.manager.request']);
    Route::delete('/manager/{id}',[ManagerController::class, 'destroy']);

    // role route
    Route::get('/role/create',[RoleController::class, 'create']);
    Route::post('/role',[RoleController::class, 'store']);
    Route::get('/role',[RoleController::class, 'index']);
    Route::get('/role/edit/{id}',[RoleController::class, 'edit']);
    Route::put('/role/{id}',[RoleController::class, 'update']);
    Route::delete('/role/{id}',[RoleController::class, 'destroy']);

    // contact route
    Route::get('/contact/create',[ContactController::class, 'create']);
    Route::post('/contact',[ContactController::class, 'store']);
    Route::get('/contact/edit/{id}',[ContactController::class, 'edit']);
    Route::put('/contact/{id}',[ContactController::class, 'update']);
    Route::delete('/contact/{id}',[ContactController::class, 'destroy']);

    // yearly leaves route
    Route::get('/yearly-leaves/create',[YearlyLeaveController::class, 'create']);
    Route::post('/yearly-leaves',[YearlyLeaveController::class, 'store']);
    Route::get('/yearly-leaves',[YearlyLeaveController::class, 'index']);
    Route::get('/yearly-leaves/edit/{id}',[YearlyLeaveController::class, 'edit']);
    Route::put('/yearly-leaves/{id}',[YearlyLeaveController::class, 'update']);
    Route::delete('/yearly-leaves/{id}',[YearlyLeaveController::class, 'destroy']);

     //leave details
    Route::get('/leave-request/details',[LeaveRequestController::class, 'leaveDetail']);

    // holiday route
    Route::get('/holiday/create',[HolidayController::class, 'create']);
    Route::post('/holiday',[HolidayController::class, 'store']);
    Route::get('/holiday',[HolidayController::class, 'index']);
    Route::get('/holiday/edit/{id}',[HolidayController::class, 'edit']);
    Route::put('/holiday/{id}',[HolidayController::class, 'update']);
    Route::delete('/holiday/{id}',[HolidayController::class, 'destroy']);

    //Search District
    Route::get('/district/search/{id?}',[SearchController::class, 'searchDistrict']);
    //Search Department
    Route::get('/department/search/{id?}',[SearchController::class, 'searchDepartment']);

    //Leave Report
    Route::get('/leave-balance-report',[LeaveReportController::class, 'leaveBalance']);
    Route::get('/no-punch-in-leave',[LeaveReportController::class, 'noPunchInNoLeave']);

    //Employees On Leave Report
    Route::get('/employees-on-leave',[LeaveReportController::class, 'employeesOnLeave']);
    
    //Carry Over Leave
    Route::get('/info',[CarryOverLeaveController::class,'calculateCarryOverLeave']);

    // fileCategory route
    Route::get('/file-category/create',[FileCategoryController::class, 'create']);
    Route::post('/file-category',[FileCategoryController::class, 'store']);
    Route::get('/file-category',[FileCategoryController::class, 'index']);
    Route::get('/file-category/edit/{id}',[FileCategoryController::class, 'edit']);
    Route::put('/file-category/{id}',[FileCategoryController::class, 'update']);
    Route::delete('/file-category/{id}',[FileCategoryController::class, 'destroy']);
});