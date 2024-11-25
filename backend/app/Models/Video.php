<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasUuids, HasFactory;

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['video_url'];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_banned' => 'boolean',
            'likes' => 'integer',
        ];
    }

    /**
     * Get the video URL attribute.
     *
     * @return string|null
     */
    public function getVideoUrlAttribute(): ?string
    {
        if ($this->s3_key) {
            // For Local Storage
            return url('storage/' . $this->s3_key);
            
            // For S3 Storage (commented out)
            // try {
            //     return Storage::disk('s3')->temporaryUrl(
            //         $this->s3_key,
            //         now()->addHours(24)
            //     );
            // } catch (\Exception $e) {
            //     return null;
            // }
        }
        return null;
    }

    /**
     * Get the user that owns the video.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tags that belong to the video.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'video_tags');
    }
}