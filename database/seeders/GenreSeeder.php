<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    public const NAMES = [
        'Pop',
        'Hip-Hop',
        'R&B',
        'Rock',
        'Electronic',
        'K-Pop',
        'Country',
        'Latin',
        'Indie',
        'Jazz',
    ];

    public function run(): void
    {
        foreach (self::NAMES as $name) {
            Genre::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
