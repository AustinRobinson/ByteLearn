<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Get video feed prioritizing user's interests
     */
    // public function index(Request $request): JsonResponse
    // {
    //     $userInterestTags = $request->user()->tags()
    //         ->where('is_banned', false)
    //         ->pluck('id');

    //     $query = Video::with(['user', 'tags'])
    //         ->where('is_banned', false);

    //     if ($userInterestTags->isNotEmpty()) {
    //         $matchingVideos = (clone $query)
    //             ->whereHas('tags', function ($q) use ($userInterestTags) {
    //                 $q->whereIn('tags.id', $userInterestTags);
    //             });

    //         $otherVideos = (clone $query)
    //             ->whereDoesntHave('tags', function ($q) use ($userInterestTags) {
    //                 $q->whereIn('tags.id', $userInterestTags);
    //             });

    //         $query = $matchingVideos->union($otherVideos);
    //     }

    //     $videos = $query->latest()
    //         ->paginate(20)
    //         ->through(function ($video) use ($userInterestTags) {
    //             return [
    //                 'id' => $video->id,
    //                 'title' => $video->title,
    //                 'description' => $video->description,
    //                 'likes' => $video->likes,
    //                 // For Local Storage
    //                 'video_url' => url('storage/' . $video->s3_key),
                    
    //                 // For S3 Storage (commented out)
    //                 // 'video_url' => Storage::disk('s3')->temporaryUrl(
    //                 //     $video->s3_key,
    //                 //     now()->addHours(24)
    //                 // ),
                    
    //                 'created_at' => $video->created_at,
    //                 'user' => [
    //                     'id' => $video->user->id,
    //                     'username' => $video->user->username,
    //                 ],
    //                 'tags' => $video->tags->map(fn($tag) => [
    //                     'id' => $tag->id,
    //                     'tag' => $tag->tag
    //                 ]),
    //                 'matches_interests' => $video->tags->whereIn('id', $userInterestTags)->isNotEmpty()
    //             ];
    //         });

    //     return response()->json([
    //         'data' => $videos->items(),
    //         'meta' => [
    //             'current_page' => $videos->currentPage(),
    //             'last_page' => $videos->lastPage(),
    //             'per_page' => $videos->perPage(),
    //             'total' => $videos->total()
    //         ]
    //     ]);
    // }

    public function index(Request $request): JsonResponse
{
    // Start with basic video query
    $query = Video::with(['user', 'tags'])
        ->where('is_banned', false);

    // For testing without auth, get the first user's tags
    $testUser = User::first();
    $userInterestTags = collect(); // Default empty collection

    if ($testUser) {
        $userInterestTags = $testUser->tags()
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
    }

    $videos = $query->latest()
        ->paginate(20)
        ->through(function ($video) use ($userInterestTags) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'likes' => $video->likes,
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
            // For Local Storage
            $videoPath = $videoFile->store('videos', 'public');
            
            // For S3 Storage (commented out)
            // $videoPath = $videoFile->store('videos', 's3');

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

            $videoData = $video->load(['user', 'tags'])->toArray();
            
            // For Local Storage
            $videoData['video_url'] = url('storage/' . $videoPath);
            
            // For S3 Storage (commented out)
            // $videoData['video_url'] = Storage::disk('s3')->temporaryUrl(
            //     $videoPath,
            //     now()->addHours(24)
            // );

            return response()->json([
                'message' => 'Video uploaded successfully',
                'data' => $videoData
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
    public function show(Video $video): JsonResponse
    {
        if ($video->is_banned) {
            return response()->json([
                'message' => 'Video not found'
            ], 404);
        }

        $video->load(['user', 'tags']);

        // For Local Storage
        $videoUrl = url('storage/' . $video->s3_key);
        
        // For S3 Storage (commented out)
        // $videoUrl = Storage::disk('s3')->temporaryUrl(
        //     $video->s3_key,
        //     now()->addHours(24)
        // );

        return response()->json([
            'data' => [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'likes' => $video->likes,
                'video_url' => $videoUrl,
                'created_at' => $video->created_at,
                'user' => [
                    'id' => $video->user->id,
                    'username' => $video->user->username,
                ],
                'tags' => $video->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'tag' => $tag->tag
                ])
            ]
        ]);
    }
}