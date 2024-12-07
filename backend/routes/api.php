<?php

use App\Http\Controllers\VideoCommentController;
use App\Http\Controllers\VideoLikeController;
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
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/refresh', 'refresh');
    Route::post('/logout', 'logout');
});

// Protected routes (require authentication)
Route::middleware([AuthenticateToken::class])->group(function () {
    // Auth routes
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'user');
    });

    // User routes
    Route::controller(UserController::class)->group(function () {
    });

    // Video feed routes
    Route::controller(VideoFeedController::class)->group(function () {
        Route::get('/video-feed/feed', "get_feed");
    });

    // Video like routes
    Route::controller(VideoLikeController::class)->group(function () {
        Route::post('/videos/like/{video}', 'store');
        Route::delete('/videos/like/{video}', 'destroy');
    });

    // Video comment routes
    Route::controller(VideoCommentController::class)->group(function () {
        Route::get('/videos/comments/{video}', 'index');
        Route::post('/videos/comments/{video}', 'store');
        Route::post('/comments/{comment}/like', 'toggleLike');
    });


    // Video routes
    Route::controller(VideoController::class)->group(function () {
        Route::get('/videos/all', 'all');
        Route::post('/videos/upload', 'upload');
        Route::get('/videos/search/all', 'searchAll');
        Route::get('/videos/search/title', 'searchTitle');
        Route::get('/videos/search/description', 'searchDescription');
        Route::get('/videos/search/tag', 'searchTag');
        Route::get('/videos/search/user', 'searchUser');
        Route::post('/videos/url', 'tempLink');
        Route::get('/videos/{id}', 'withId');
    });

    // Tag routes
    Route::apiResource('tags', TagController::class)->only(['index', 'store', 'destroy']);
    Route::controller(TagController::class)->group(function () {
        Route::post('users/{user}/tags', 'attachToUser');
        Route::delete('users/{user}/tags', 'detachFromUser');
    });


});

