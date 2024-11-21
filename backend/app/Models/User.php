<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_until' => 'datetime',
            'permanently_banned_at' => 'datetime',
        ];
    }

    /**
     * The tags that belong to the user.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'user_interest');
    }

    /**
     * The videos the user owns.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    /**
     * The user's tokens.
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    /**
     * The strikes on the user's accounts.
     */
    public function userStrikes(): HasMany
    {
        return $this->hasMany(UserStrikes::class);
    }

    /**
     * The videos that the user has liked.
     */
    public function videosLiked(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'user_video_like');
    }

    /**
     * The videos that the user has watched.
     */
    public function videosWatched(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'user_watched_video');
    }

    /**
     * The users this user follows.
     */
    public function usersFollows(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'creator_id');
    }

    /**
     * The users this user is followed by.
     */
    public function usersFollowedBy(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'user_follows', 'creator_id', 'follower_id');
    }

    /**
     * The video reports the user has made.
     */
    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'video_reports');
    }
}
