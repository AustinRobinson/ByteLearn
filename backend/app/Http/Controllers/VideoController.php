<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
     * Get the details associated with a video with the given ID.
     */
    public function withId(Request $request, string $id): JsonResponse
    {
        $video = Video::with(['user:id,username', 'tags:id,tag'])
            ->withCount(['likedBy'])
            ->where('id', $id)
            ->first();

        $user = $request->user();

        return response()->json([
            'message' => 'Video retrieved',
            'data' => [
                'id' => $video->id,
                's3_key' => $video->s3_key,
                'title' => $video->title,
                'description' => $video->description,
                'created_at' => $video->created_at,
                'user' => [
                    'id' => $video->user->id,
                    'username' => $video->user->username,
                ],
                'tags' => $video->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->tag,
                    ];
                }),
                'has_watched' => $video->watchedBy()->where('user_id', $user->id)->exists(),
                'is_liked' => $video->likedBy()->where('user_id', $user->id)->exists(),
                'like_count' => $video->likes
            ],
        ], 200);
    }

    /**
     * Get the temporary video link
     */
    public function tempLink(Request $request): JsonResponse {
        $validated = $request->validate([
            's3_key' => ['required', 'string', 'max:255']
        ]);

        $url = Storage::disk('s3-videos')->temporaryUrl($validated['s3_key'], now()->addMinutes(5));

        return response()->json([
            'message' => 'Temporary video link for '.$validated['s3_key'],
            'data' => $url
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'video' => ['required', 'file', 'mimetypes:video/mp4'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string']
        ]);

        $user = $request->user();
        $video_file = $request->file('video');

        $path = Storage::disk('s3-videos')->putFile($user->id, $video_file);

        $video = Video::create([
            'user_id' => $user->id,
            's3_key' => $path,
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
