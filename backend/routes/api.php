<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Tag;
use App\Models\Video;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\VideoFeedController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateToken;

// Unprotected routes
Route::controller(AuthController::class)->group(function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/refresh', 'refresh');
    Route::post('/logout', 'logout');
});

// Protected routes (require authentication)
Route::middleware([AuthenticateToken::class])->group(function () {
    // Auth routes
    Route::controller(AuthController::class)->group(function() {
        Route::get('/user', 'user');
    });

    // User routes
    Route::controller(UserController::class)->group(function() {
    });

    // Video routes
    Route::controller(VideoController::class)->group(function() {
        Route::get('/videos/all', 'all');
        Route::post('/videos/upload', 'upload');
        Route::get('/videos/search/all', 'searchAll');
        Route::get('/videos/search/title', 'searchTitle');
        Route::get('/videos/search/description', 'searchDescription');
        Route::get('/videos/search/tag', 'searchTag');
        Route::get('/videos/search/user', 'searchUser');
    });

    // Tag routes
    Route::apiResource('tags', TagController::class)->only(['index', 'store', 'destroy']);
    Route::post('users/{user}/tags', [TagController::class, 'attachToUser']);
    Route::delete('users/{user}/tags', [TagController::class, 'detachFromUser']);

    // Video feed, likes and comments
    Route::get('/videos/feed', VideoFeedController::class);
    Route::post('/videos/like/{video}', [VideoLikeController::class, 'store']);
    Route::delete('/videos/like/{video}', [VideoLikeController::class, 'destroy']);
    Route::get('/videos/comments/{video}', [VideoCommentController::class, 'index']);
    Route::post('/videos/comments/{video}', [VideoCommentController::class, 'store']);
    Route::post('/comments/{comment}/like', [VideoCommentController::class, 'toggleLike']);
});