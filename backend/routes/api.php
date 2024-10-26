<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\TagController;

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