<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Video extends Model
{
    use HasFactory, HasUuids, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        's3_key',
        'title',
        'description',
        'likes',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_banned' => 'boolean',
        ];
    }

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
     * The user that owns the video.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users that have liked the video.
     */
    public function usersLiked(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_video_like');
    }

    /**
     * The tags that the video has.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'video_tag');
    }

    /**
     * The users that haved watched the video.
     */
    public function usersWatched(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_watched_video');
    }

    /**
     * The users who have reported the video.
     */
    public function usersReported(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'video_reports');
    }

    /**
     * The comments on the video.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The playlists that the videos are in.
     */
    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_video');
    }
}
