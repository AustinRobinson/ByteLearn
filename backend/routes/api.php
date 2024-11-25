<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Tag;
use App\Models\Video;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AuthenticateToken;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/logout', [AuthController::class, 'logout']);

// protected routes (require authentication)
Route::middleware([AuthenticateToken::class])->group(function () {
    // user routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/{id}', function (Request $request, string $id) {
        return User::all();
    });

    // new tag routes
    Route::apiResource('tags', TagController::class)->only(['index', 'store', 'destroy']);
    Route::post('users/{user}/tags', [TagController::class, 'attachToUser']);
    Route::delete('users/{user}/tags', [TagController::class, 'detachFromUser']);
});

//video routes
// Route::middleware(['auth:api'])->group(function () {
//     Route::post('videos', [VideoController::class, 'store'])->middleware('video.upload');
//     Route::get('videos', [VideoController::class, 'index']);
//     Route::get('videos/{video}', [VideoController::class, 'show']);
// });
Route::get('/videos', [VideoController::class, 'index']);
Route::post('/videos', [VideoController::class, 'store']);
Route::get('/videos/{video}', [VideoController::class, 'show']);