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
Route::get('/getprofiles', [ProfileController::class, 'index']);

Route::middleware(['auth:sanctum', 'checkAdmin'])->group(function () {
    Route::post('/profiles', [ProfileController::class, 'store']);  // Protected route, only admins can create profiles
    Route::post('/profiles/{profile}/comments', [ProfileController::class, 'storeComment']);  // Protected route, only admins can create comments for profiles
});
Route::middleware('auth:sanctum')->get('/profiles', [ProfileController::class, 'indexAll']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/updateProfiles/{profile}', [ProfileController::class, 'update']);  // Update profile
    Route::delete('/deleteProfiles/{profile}', [ProfileController::class, 'destroy']);  // delete  profile
});

