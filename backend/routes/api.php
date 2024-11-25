<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Tag;
use App\Models\Video;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VideoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Safe route
// Route::get('/user/{id}', function (Request $request, string $id) {
//     return User::all();
// })->middleware('auth:api');

// Unsafe route
Route::get('/user/{id}', function (Request $request, string $id) {
    return User::all();
});

// New Tag routes
Route::middleware('auth:api')->group(function () {
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