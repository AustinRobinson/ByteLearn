<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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
