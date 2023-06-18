<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\EmployeesController;
use App\Models\Post;

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

Route::post('/register', [EmployeesController::class, 'register']);

Route::post('/login', [EmployeesController::class, 'login']);
Route::group(['middleware' => ['jwt.auth', 'checkadmin']], function () {
    Route::post('/refresh', [EmployeesController::class, 'refresh']);

});
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/profile', [EmployeesController::class, 'profile']);
    Route::get('/add', [EmployeesController::class, 'create']);
    Route::post('/change', [EmployeesController::class, 'change']);// thay doi password
    Route::get('/users', [EmployeesController::class, 'getUsers']); // filter
    Route::post('/logout', [EmployeesController::class, 'logout']);


});

Route::group(['middleware' => ['jwt.auth', 'check']], function () {
    Route::delete('/delete/{id}', [EmployeesController::class, 'delete']);
    Route::get('/get-users',[PostController::class,'index']);
    Route::put('/post/{id}', [PostController::class, 'update']);// cap nhat roles
});

//Router Leaves
Route::get('/leaves', [LeaveController::class, 'index']);// search
Route::get('/leaves/{id}', [LeaveController::class, 'show']);
Route::post('/leaves', [LeaveController::class, 'store']);
Route::put('/leaves/{id}', [LeaveController::class, 'update']);
Route::delete('/leaves/{id}', [LeaveController::class, 'destroy']);




