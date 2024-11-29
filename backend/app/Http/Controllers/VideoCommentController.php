<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoCommentController extends Controller
{
    /**
     * Get video comments
     */
    public function index(Video $video): JsonResponse
    {
        $comments = $video->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('comment_id') // Get only parent comments
            ->get();

        return response()->json([
            'data' => $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'text' => $comment->comment,
                    'user' => [
                        'id' => $comment->user->id,
                        'username' => $comment->user->username
                    ],
                    'replies' => $comment->replies->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'text' => $reply->comment,
                            'user' => [
                                'id' => $reply->user->id,
                                'username' => $reply->user->username
                            ]
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * Add a comment to video
     */
    public function store(Request $request, Video $video): JsonResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string'],
            'comment_id' => ['sometimes', 'exists:comments,id'] // For replies
        ]);

        $comment = new Comment([
            'comment' => $validated['comment'],
            'comment_id' => $validated['comment_id'] ?? null
        ]);

        $comment->user()->associate($request->user());
        $comment->video()->associate($video);
        $comment->save();

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => [
                'id' => $comment->id,
                'text' => $comment->comment,
                'user' => [
                    'id' => $comment->user->id,
                    'username' => $comment->user->username
                ]
            ]
        ], 201);
    }
}