<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\AuthenticateToken;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/logout', [AuthController::class, 'logout']);

// protected routes (require authentication)
Route::middleware([AuthenticateToken::class])->group(function () {
    // user routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/all', function (Request $request) {
        return User::all();
    });

    Route::controller(VideoController::class)->group(function() {
        Route::get('/videos/all', 'all');
    });

    // new tag routes
    Route::apiResource('tags', TagController::class)->only(['index', 'store', 'destroy']);
    Route::post('users/{user}/tags', [TagController::class, 'attachToUser']);
    Route::delete('users/{user}/tags', [TagController::class, 'detachFromUser']);
});