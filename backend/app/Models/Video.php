<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class Video extends Model
{
    use HasFactory, HasUuids, Searchable;

    protected $fillable = [
        'user_id',
        's3_key',
        'title',
        'description',
        'likes',
        'is_banned',
    ];

    protected $casts = [
        'is_banned' => 'boolean',
        'likes' => 'integer',
    ];

    /**
     * Set attributes that should be searchable by Meilisearch
     * 
     * @return array
     */
    public function toSearchableArray(): array {
        return [
            "title" => $this->title,
            "description" => $this->description,
            "tags" => $this->tags,
            "user" => $this->user->username
        ];
    }


    /**
     * Scope for random video feed with pagination support
     */
    public function scopeRandomFeed($query, $seed = 0)
    {
        return $query->inRandomOrder($seed);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'video_tag');
    }

    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_video_like');
    }

    public function watchedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_watched_video')
            ->withPivot('watched_at');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_video')
            ->withPivot(['added_at', 'order']);
    }
}
