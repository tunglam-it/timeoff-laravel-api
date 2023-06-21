<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LeaveController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => ['jwt.auth', 'checkadmin']], function () {
    Route::post('/refresh', [AuthController::class, 'refresh']);

});
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/show/{id}', [EmployeesController::class, 'show']);
    Route::post('/change', [EmployeesController::class, 'changePassword']);// thay doi password
    Route::get('/get-users',[EmployeesController::class,'index']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //Router Leaves
    Route::get('/leaves', [LeaveController::class, 'index']);// search
    Route::get('/leaves/{id}', [LeaveController::class, 'show']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    Route::put('/leaves/{id}', [LeaveController::class, 'update']);
    Route::delete('/leaves/{id}', [LeaveController::class, 'destroy']);
    Route::get('/by-user-id',[LeaveController::class,'getLeavesByUserId']);
    Route::get('/calc-time',[LeaveController::class,'calc_time']);

});

Route::group(['middleware' => ['jwt.auth', 'check']], function () {
    Route::delete('/delete/{id}', [EmployeesController::class, 'delete']);
    Route::put('/update/{id}', [EmployeesController::class, 'update']);// cap nhat roles
});






