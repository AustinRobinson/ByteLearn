<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends Model
{
    use HasUuids;

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
}
