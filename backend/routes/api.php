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

// New Tag routes
Route::post('/tags', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'tag' => ['required', 'string', 'max:255', 'unique:tags,tag']
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $tag = Tag::create([
            'tag' => $request->tag,
            'is_banned' => false
        ]);

        return response()->json([
            'message' => 'Tag created successfully',
            'data' => $tag
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error creating tag',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth:api');

Route::get('/tags', function (Request $request) {
    try {
        $tags = Tag::all();
        return response()->json([
            'data' => $tags
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error retrieving tags',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth:api');

//User model will automatically handle UUID route binding
Route::post('/users/{user}/tags', function (Request $request, User $user) {
    $validator = Validator::make($request->all(), [
        'tag_ids' => ['required', 'array'],
        'tag_ids.*' => ['required', 'uuid', 'exists:tags,id'] // Updated to validate UUIDs
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $user->tags()->attach($request->tag_ids);
        return response()->json([
            'message' => 'Tags attached successfully',
            'data' => $user->load('tags')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error attaching tags',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth:api');

Route::delete('/users/{user}/tags', function (Request $request, User $user) {
    $validator = Validator::make($request->all(), [
        'tag_ids' => ['required', 'array'],
        'tag_ids.*' => ['required', 'uuid', 'exists:tags,id'] // Updated to validate UUIDs
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $user->tags()->detach($request->tag_ids);
        return response()->json([
            'message' => 'Tags detached successfully',
            'data' => $user->load('tags')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error detaching tags',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth:api');

Route::delete('/tags/{tag}', function (Request $request, Tag $tag) {
    try {
        $tag->users()->detach();
        $tag->delete();
        return response()->json([
            'message' => 'Tag deleted successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error deleting tag',
            'error' => $e->getMessage()
        ], 500);
    }
})->middleware('auth:api');