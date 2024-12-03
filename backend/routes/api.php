<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Tag;
use App\Models\Video;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoFeedController;
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

    // tag routes
    Route::apiResource('tags', TagController::class)->only(['index', 'store', 'destroy']);
    Route::post('users/{user}/tags', [TagController::class, 'attachToUser']);
    Route::delete('users/{user}/tags', [TagController::class, 'detachFromUser']);

    // Video routes
    Route::get('/videos/feed', VideoFeedController::class);

    // Video likes 
    Route::post('/videos/like/{video}', [VideoLikeController::class, 'store']);
    Route::delete('/videos/like/{video}', [VideoLikeController::class, 'destroy']);

    // Video comments
    Route::get('/videos/comments/{video}', [VideoCommentController::class, 'index']);
    Route::post('/videos/comments/{video}', [VideoCommentController::class, 'store']);
});

