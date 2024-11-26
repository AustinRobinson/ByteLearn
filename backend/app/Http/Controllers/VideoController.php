<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Get personalized video feed
     */
    public function index(Request $request): JsonResponse
    {
        $query = Video::with(['user', 'tags'])
            ->where('is_banned', false);

        // Get authenticated user's interests
        $user = $request->user();
        $userInterestTags = $user->interests()
            ->where('is_banned', false)
            ->pluck('id');

        if ($userInterestTags->isNotEmpty()) {
            $matchingVideos = (clone $query)
                ->whereHas('tags', function ($q) use ($userInterestTags) {
                    $q->whereIn('tags.id', $userInterestTags);
                });

            $otherVideos = (clone $query)
                ->whereDoesntHave('tags', function ($q) use ($userInterestTags) {
                    $q->whereIn('tags.id', $userInterestTags);
                });

            $query = $matchingVideos->union($otherVideos);
        }

        $videos = $query->latest()
            ->paginate(20)
            ->through(function ($video) use ($user) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'video_url' => url('storage/' . $video->s3_key),
                    'created_at' => $video->created_at,
                    'user' => [
                        'id' => $video->user->id,
                        'username' => $video->user->username,
                    ],
                    'tags' => $video->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'tag' => $tag->tag
                    ]),
                    'is_liked' => $video->likedBy()->where('user_id', $user->id)->exists(),
                    'has_watched' => $video->watchedBy()->where('user_id', $user->id)->exists(),
                    'comment_count' => $video->comments()->count(),
                    'matches_interests' => $video->tags->whereIn('id', $userInterestTags)->isNotEmpty()
                ];
            });

        return response()->json([
            'data' => $videos->items(),
            'meta' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total()
            ]
        ]);
    }

    /**
     * Store a new video
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['exists:tags,id']
        ]);

        if (!$request->hasFile('video_file')) {
            return response()->json([
                'message' => 'Video file is required'
            ], 422);
        }

        $videoFile = $request->file('video_file');

        if ($videoFile->getSize() > 100 * 1024 * 1024) {
            return response()->json([
                'message' => 'Video file size must not exceed 100MB'
            ], 413);
        }

        if (!in_array($videoFile->getMimeType(), [
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo'
        ])) {
            return response()->json([
                'message' => 'Invalid video format. Supported formats: MP4, MOV, AVI'
            ], 422);
        }

        try {
            $videoPath = $videoFile->store('videos', 'public');
            
            $video = Video::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                's3_key' => $videoPath,
                'likes' => 0,
                'is_banned' => false
            ]);

            if (isset($validated['tag_ids'])) {
                $video->tags()->attach($validated['tag_ids']);
            }

            return response()->json([
                'message' => 'Video uploaded successfully',
                'data' => array_merge($video->load(['user', 'tags'])->toArray(), [
                    'video_url' => url('storage/' . $videoPath),
                    'comment_count' => 0,
                    'is_liked' => false,
                    'has_watched' => false
                ])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error uploading video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific video
     */
    public function show(Video $video, Request $request): JsonResponse
    {
        if ($video->is_banned) {
            return response()->json([
                'message' => 'Video not found'
            ], 404);
        }

        $video->load(['user', 'tags', 'comments.user', 'comments.replies.user']);
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'video_url' => url('storage/' . $video->s3_key),
                'created_at' => $video->created_at,
                'user' => [
                    'id' => $video->user->id,
                    'username' => $video->user->username,
                ],
                'tags' => $video->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'tag' => $tag->tag
                ]),
                'is_liked' => $video->likedBy()->where('user_id', $user->id)->exists(),
                'has_watched' => $video->watchedBy()->where('user_id', $user->id)->exists(),
                'comments' => $video->comments->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'text' => $comment->comment,
                        'user' => [
                            'id' => $comment->user->id,
                            'username' => $comment->user->username
                        ],
                        'replies' => $comment->replies->map(function($reply) {
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
            ]
        ]);
    }
}