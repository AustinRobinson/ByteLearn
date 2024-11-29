<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoLikeController extends Controller
{
    /**
     * Like a video
     */
    public function store(Request $request, Video $video): JsonResponse
    {
        $user = $request->user();
        
        if (!$video->likedBy()->where('user_id', $user->id)->exists()) {
            $video->likedBy()->attach($user->id);
            $video->increment('likes');
        }

        return response()->json([
            'message' => 'Video liked successfully'
        ]);
    }

    /**
     * Unlike a video
     */
    public function destroy(Request $request, Video $video): JsonResponse
    {
        $user = $request->user();
        
        if ($video->likedBy()->where('user_id', $user->id)->exists()) {
            $video->likedBy()->detach($user->id);
            $video->decrement('likes');
        }

        return response()->json([
            'message' => 'Video unliked successfully'
        ]);
    }
}