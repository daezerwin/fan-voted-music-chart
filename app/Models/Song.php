<?php

namespace App\Models;

use Database\Factories\SongFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'artist_id',
    'genre_id',
    'title',
    'slug',
    'youtube_video_id',
    'release_date',
    'cover_image',
    'description',
    'is_active',
    'voting_enabled',
    'is_featured',
])]
class Song extends Model
{
    /** @use HasFactory<SongFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * @return BelongsTo<Genre, $this>
     */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * @return HasMany<Vote, $this>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'release_date' => 'date:Y-m-d',
            'is_active' => 'boolean',
            'voting_enabled' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }
}
