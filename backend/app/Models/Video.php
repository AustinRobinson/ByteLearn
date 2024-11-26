<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasUuids, HasFactory;

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
        return $this->belongsToMany(User::class, 'user_watched_video')->withPivot('watched_at');
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
