<?php

use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('my-details', [UserController::class, 'myDetails']);
    Route::get('get-users', [UserController::class, 'getUsers']);
    Route::Put('update-user', [UserController::class, 'update']);
    Route::apiResource('todos', TodoController::class);
});
