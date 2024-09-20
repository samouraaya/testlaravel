<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/addUser', [UserController::class, 'addUser']);

Route::middleware(['auth:sanctum', 'checkAdmin'])->group(function () {
    Route::post('/profiles', [ProfileController::class, 'store']);  // Protected route, only admins can create profiles
    Route::post('/profiles/{profile}/comments', [CommentController::class, 'store']);  // Protected route, only admins can create comments for profiles
});

/*Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});*/
