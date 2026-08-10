<?php

namespace App\Models;

use App\Enums\ChartType;
use Database\Factories\ChartFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['chart_type', 'chart_date', 'generated_at'])]
class Chart extends Model
{
    /** @use HasFactory<ChartFactory> */
    use HasFactory;

    /**
     * @return HasMany<ChartEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(ChartEntry::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'chart_type' => ChartType::class,
            'chart_date' => 'date:Y-m-d',
            'generated_at' => 'datetime',
        ];
    }
}
