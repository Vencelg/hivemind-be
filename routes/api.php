<?php

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

Route::group(['prefix' => 'auth'], function () {
    Route::get('', [\App\Http\Controllers\AuthController::class, 'index'])->middleware("auth:sanctum");
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::get('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware("auth:sanctum");
    Route::post('userProfile', [\App\Http\Controllers\AuthController::class, 'userProfile'])->middleware("auth:sanctum");
    Route::post('editUser', [\App\Http\Controllers\AuthController::class, 'editUser'])->middleware("auth:sanctum");
});

Route::group(['prefix' => 'verify', 'middleware' => 'auth:sanctum'], function () {
    Route::post('resend', [\App\Http\Controllers\EmailVerificationController::class, 'resend']);
    Route::get('verify-email/{id}/{hash}', [\App\Http\Controllers\EmailVerificationController::class, 'verify'])->name('verification.verify');
});

Route::group(['middleware' => ['auth:sanctum', 'isVerified']], function () {
    
    Route::post('friends', [\App\Http\Controllers\FriendController::class, 'store']);
    Route::put('friends', [\App\Http\Controllers\FriendController::class, 'update']);
    Route::delete('friends', [\App\Http\Controllers\FriendController::class, 'destroy']);

    Route::apiResources([
        'posts' => \App\Http\Controllers\PostController::class,
        'comments' => \App\Http\Controllers\CommentController::class,
        'responses' => \App\Http\Controllers\ResponseController::class,
    ]);

    Route::get('users/{searchKey}', [\App\Http\Controllers\SearchController::class, 'search']);
});

