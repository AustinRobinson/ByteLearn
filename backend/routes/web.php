<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{any}', function () {
    $path = public_path('frontend/index.html');
    if (File::exists($path)) {
        return Response::file($path);
    }
    abort(404);
})->where('any', '^(?!api).*$');

