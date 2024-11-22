<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStrike extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'reason',
        'has_caused_suspension',
    ];

    // timestamps - created_at, no updated_at
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'has_caused_suspension' => 'boolean',
        ];
    }

    /**
     * The user that this strike is associated with.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
