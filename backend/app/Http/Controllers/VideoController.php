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
        ], 200);
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
     * Search over title, description, tag, and user
     */
    public function searchAll(Request $request): JsonResponse
    {
        $search = Video::search($request->search)->get();

        return response()->json([
            'message' => 'Search results for '.$request->search,
            'data' => $search
        ], 200);
    }

    /**
     * Search over title only
     */
    public function searchTitle(Request $request): JsonResponse {
        $search = Video::search($request->search)->options([
            'attributesToSearchOn' => ['title']
        ])->get();
        
        return response()->json([
            'message' => 'Search results on titles for '.$request->search,
            'data' => $search
        ], 200);

    }

    /**
     * Search over description only
     */
    public function searchDescription(Request $request): JsonResponse {
        $search = Video::search($request->search)->options([
            'attributesToSearchOn' => ['description']
        ])->get();
        
        return response()->json([
            'message' => 'Search results on descriptions for '.$request->search,
            'data' => $search
        ], 200);
    }

    /**
     * Search over tag only
     */
    public function searchTag(Request $request): JsonResponse {
        $search = Video::search($request->search)->options([
            'attributesToSearchOn' => ['tags']
        ])->get();
        
        return response()->json([
            'message' => 'Search results on tags for '.$request->search,
            'data' => $search
        ], 200);
    }

    /**
     * Search over user only
     */
    public function searchUser(Request $request): JsonResponse {
        $search = Video::search($request->search)->options([
            'attributesToSearchOn' => ['user']
        ])->get();
        
        return response()->json([
            'message' => 'Search results on users for '.$request->search,
            'data' => $search
        ], 200);
    }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, Video $video)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Video $video)
    // {
    //     //
    // }
}
