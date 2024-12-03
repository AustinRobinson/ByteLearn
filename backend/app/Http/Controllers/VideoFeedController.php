<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoFeedController extends Controller
{
    /**
     * Get personalized video feed
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'offset' => ['sometimes', 'integer', 'min:0'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100']
        ]);

        $limit = $validated['limit'] ?? 20;
        $offset = $validated['offset'] ?? 0;
        
        // Get user's interests
        $user = $request->user();
        $userInterestTags = $user->interests()
            ->where('is_banned', false)
            ->pluck('id')
            ->toArray();

        $query = Video::with(['user', 'tags'])
            ->where('is_banned', false);

        if (!empty($userInterestTags)) {
            // Get videos matching user interests first
            $matchingVideos = (clone $query)
                ->whereHas('tags', function ($q) use ($userInterestTags) {
                    $q->whereIn('tags.id', $userInterestTags);
                });

            // Then get videos that don't match interests
            $otherVideos = (clone $query)
                ->whereDoesntHave('tags', function ($q) use ($userInterestTags) {
                    $q->whereIn('tags.id', $userInterestTags);
                });

            $query = $matchingVideos->union($otherVideos);
        } else {
            $query = $query->randomFeed($offset / $limit);
        }

        // Apply pagination
        $total = $query->count();
        $videos = $query->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json([
            'data' => $videos->map(function ($video) use ($user, $userInterestTags) {
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
                    'matches_interests' => !empty($userInterestTags) && 
                        $video->tags->whereIn('id', $userInterestTags)->isNotEmpty()
                ];
            }),
            'meta' => [
                'total' => $total,
                'offset' => $offset,
                'limit' => $limit,
                'has_interests' => !empty($userInterestTags)
            ]
        ]);
    }
}