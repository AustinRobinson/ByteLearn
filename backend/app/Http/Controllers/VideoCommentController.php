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
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $comments->map(function ($comment) use ($video) {
                return [
                    'id' => $comment->id,
                    'text' => $comment->comment,
                    'is_user_creator' => $comment->user_id === request()->user()->id,
                    'user' => [
                        'id' => $comment->user->id,
                        'username' => $comment->user->username
                    ],
                    'likes' => $comment->usersLiked()->count(),
                    'is_liked' => $comment->usersLiked()->where('user_id', request()->user()->id)->exists(),
                    'created_at' => $comment->created_at,
                    'replies' => $comment->replies->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'text' => $reply->comment,
                            'is_user_creator' => $reply->user_id === request()->user()->id,
                            'user' => [
                                'id' => $reply->user->id,
                                'username' => $reply->user->username
                            ]
                        ];
                    })
                ];
            }),
            'meta' => [
                'total_comments' => $video->comments()->count()
            ]
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

    /**
     * Toggle like status on a comment
     */
    public function toggleLike(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        if ($comment->usersLiked()->where('user_id', $user->id)->exists()) {
            $comment->usersLiked()->detach($user->id);
            $comment->decrement('likes');
            $message = 'Comment unliked successfully';
        } else {
            $comment->usersLiked()->attach($user->id);
            $comment->increment('likes');
            $message = 'Comment liked successfully';
        }
        return response()->json(['message' => $message]);
    }
}
