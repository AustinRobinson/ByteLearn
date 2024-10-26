<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * Create a new tag.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tag' => ['required', 'string', 'max:255', 'unique:tags,tag']
        ]);

        $tag = Tag::create([
            'tag' => $validated['tag'],
            'is_banned' => false
        ]);

        return response()->json([
            'message' => 'Tag created successfully',
            'data' => $tag
        ], 201);
    }

    /**
     * Get all tags.
     */
    public function index(): JsonResponse
    {
        $tags = Tag::all();
        return response()->json(['data' => $tags]);
    }

    /**
     * Attach tags to a user.
     */
    public function attachToUser(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'tag_ids' => ['required', 'array'],
            'tag_ids.*' => ['required', 'uuid', 'exists:tags,id']
        ]);

        $user->tags()->attach($validated['tag_ids']);

        return response()->json([
            'message' => 'Tags attached successfully',
            'data' => $user->load('tags')
        ]);
    }

    /**
     * Detach tags from a user.
     */
    public function detachFromUser(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'tag_ids' => ['required', 'array'],
            'tag_ids.*' => ['required', 'uuid', 'exists:tags,id']
        ]);

        $user->tags()->detach($validated['tag_ids']);

        return response()->json([
            'message' => 'Tags detached successfully',
            'data' => $user->load('tags')
        ]);
    }

    /**
     * Delete a tag.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->users()->detach();
        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully'
        ]);
    }
}