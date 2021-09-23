<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DesignationController;
use App\Http\Controllers\OrganizationController;

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
    return view('welcome');
});

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