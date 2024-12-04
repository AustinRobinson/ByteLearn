<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Tag extends Model
{
    use HasFactory, HasUuids, Searchable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tag',
        'is_banned',
    ];

    /**
     * Set attributes that should be searchable by Meilisearch
     * 
     * @return array
     */
    public function toSearchableArray(): array {
        return [
            "tag" => $this->tag,
        ];
    }

    /**
     * Indicates if timestamps created_at and updated_at should be created.
     *
     * @var bool
     */
    public $timestamps = false;

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
     * The users that belong to the tag.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_interest');
    }

    /**
     * The videos having the tag.
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'video_tag');
    }
}
