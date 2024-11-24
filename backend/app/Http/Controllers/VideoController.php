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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'uuid'],
            's3' => ['required', 'string', 'unique:videos', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string']
        ]);

        $video = Video::create([
            'user_id' => $validated['user_id'],
            's3' => $validated['s3'],
            'title' => $validated['title'],
            'description' => $validated['description']
        ]);

        return response()->json([
            'message' => 'Video created successfully.',
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
