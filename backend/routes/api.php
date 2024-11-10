<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AuthenticateToken;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/logout', [AuthController::class, 'logout']);





// Protected Routes (Require JWT Authentication)
Route::middleware([AuthenticateToken::class])->group(function () {
    // user routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/{id}', function (Request $request, string $id) {
        return User::all();
    });

    // New Tag routes
    Route::apiResource('tags', TagController::class)->only(['index', 'store', 'destroy']);
    Route::post('users/{user}/tags', [TagController::class, 'attachToUser']);
    Route::delete('users/{user}/tags', [TagController::class, 'detachFromUser']);
});


// Unsafe route







// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

// Safe route
// Route::get('/user/{id}', function (Request $request, string $id) {
//     return User::all();
// })->middleware('auth:api');




// Route::middleware('auth:api')->group(function () {
    
// });