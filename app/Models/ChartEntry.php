<?php

namespace App\Models;

use Database\Factories\ChartEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['chart_id', 'song_id', 'rank', 'vote_count', 'previous_rank', 'movement', 'peak_rank', 'charting_periods'])]
class ChartEntry extends Model
{
    /** @use HasFactory<ChartEntryFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Chart, $this>
     */
    public function chart(): BelongsTo
    {
        return $this->belongsTo(Chart::class);
    }

    /**
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function isNewEntry(): bool
    {
        return $this->previous_rank === null && $this->charting_periods === 1;
    }

    public function isReentry(): bool
    {
        return $this->previous_rank === null && $this->charting_periods > 1;
    }
}
