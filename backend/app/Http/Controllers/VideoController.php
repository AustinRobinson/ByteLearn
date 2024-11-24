<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoController extends Controller
{

    public function all(Request $request): JsonResponse
    {

        $data = Video::all();

        return response()->json([
            'message' => "Returning all videos.",
            'data' => $data
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            's3_key' => ['required', 'string', 'unique:videos', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string']
        ]);

        $user = $request->user();

        $video = Video::create([
            'user_id' => $user->id,
            's3_key' => $validated['s3_key'],
            'title' => $validated['title'],
            'description' => $validated['description']
        ])->user()->associate($user);

        return response()->json([
            'message' => 'Video uploaded successfully.',
            'data' => $video
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        //
    }
}
