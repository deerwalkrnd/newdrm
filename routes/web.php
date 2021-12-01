<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DesignationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\YearlyLeaveController;
use App\Http\Controllers\AttendanceController;


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

Route::middleware(['logged-in','hr'])->group(function(){
    
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

    // service type route
    Route::get('/serviceType/create',[ServiceTypeController::class, 'create']);
    Route::post('/serviceType',[ServiceTypeController::class, 'store']);
    Route::get('/serviceType',[ServiceTypeController::class, 'index']);
    Route::get('/serviceType/edit/{id}',[ServiceTypeController::class, 'edit']);
    Route::put('/serviceType/{id}',[ServiceTypeController::class, 'update']);
    Route::delete('/serviceType/{id}',[ServiceTypeController::class, 'destroy']);

    // service type route
    Route::get('/shift/create',[ShiftController::class, 'create']);
    Route::post('/shift',[ShiftController::class, 'store']);
    Route::get('/shift',[ShiftController::class, 'index']);
    Route::get('/shift/edit/{id}',[ShiftController::class, 'edit']);
    Route::put('/shift/{id}',[ShiftController::class, 'update']);
    Route::delete('/shift/{id}',[ShiftController::class, 'destroy']);

    // manager route
    Route::get('/manager/create',[ManagerController::class, 'create']);
    Route::post('/manager',[ManagerController::class, 'store']);
    Route::get('/manager',[ManagerController::class, 'index']);
    Route::get('/manager/edit/{id}',[ManagerController::class, 'edit']);
    Route::put('/manager/{id}',[ManagerController::class, 'update']);
    Route::delete('/manager/{id}',[ManagerController::class, 'destroy']);

    // role route
    Route::get('/role/create',[RoleController::class, 'create']);
    Route::post('/role',[RoleController::class, 'store']);
    Route::get('/role',[RoleController::class, 'index']);
    Route::get('/role/edit/{id}',[RoleController::class, 'edit']);
    Route::put('/role/{id}',[RoleController::class, 'update']);
    Route::delete('/role/{id}',[RoleController::class, 'destroy']);

    // yearly leaves route
    Route::get('/yearly-leaves/create',[YearlyLeaveController::class, 'create']);
    Route::post('/yearly-leaves',[YearlyLeaveController::class, 'store']);
    Route::get('/yearly-leaves',[YearlyLeaveController::class, 'index']);
    Route::get('/yearly-leaves/edit/{id}',[YearlyLeaveController::class, 'edit']);
    Route::put('/yearly-leaves/{id}',[YearlyLeaveController::class, 'update']);
    Route::delete('/yearly-leaves/{id}',[YearlyLeaveController::class, 'destroy']);

    Route::get('/punch-in',[AttendanceController::class, 'index']);
    Route::post('/punch-in',[AttendanceController::class, 'punchIn']);
    Route::post('/punch-out',[AttendanceController::class, 'punchOut']);
});



// Route::get('/test',function(){
//     if(Auth::user())
//         return "Authenticated";
//     else
//         return "Log In ?";
// });