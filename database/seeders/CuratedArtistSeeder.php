<?php

namespace Database\Seeders;

use App\Models\Artist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CuratedArtistSeeder extends Seeder
{
    /**
     * Real artists backing the real songs in CuratedSongSeeder. No Faker
     * dependency, so — unlike ArtistSeeder — this is safe to run in
     * production.
     *
     * @var list<array{name: string, country: string, bio: string, website: ?string}>
     */
    public const ARTISTS = [
        [
            'name' => 'Hale',
            'country' => 'PH',
            'bio' => 'Filipino rock band formed in 2003, best known for "The Day You Said Goodnight."',
            'website' => null,
        ],
        [
            'name' => 'Cueshe',
            'country' => 'PH',
            'bio' => 'OPM band from Cebu best known for the single "Stay."',
            'website' => null,
        ],
        [
            'name' => 'Shamrock',
            'country' => 'PH',
            'bio' => 'Filipino rock band best known for the single "Alipin."',
            'website' => null,
        ],
        [
            'name' => 'Maroon 5',
            'country' => 'US',
            'bio' => 'American pop rock band fronted by Adam Levine, formed in 1994.',
            'website' => 'https://www.maroon5.com',
        ],
        [
            'name' => 'Backstreet Boys',
            'country' => 'US',
            'bio' => 'American vocal group formed in 1993, one of the best-selling boy bands of all time.',
            'website' => 'https://www.backstreetboys.com',
        ],
        [
            'name' => 'Westlife',
            'country' => 'IE',
            'bio' => 'Irish boy band formed in 1998, known for ballads like "Flying Without Wings."',
            'website' => 'https://www.westlife.com',
        ],
        [
            'name' => 'Katy Perry',
            'country' => 'US',
            'bio' => 'American singer known for pop hits like "Firework" and "Roar."',
            'website' => 'https://www.katyperry.com',
        ],
    ];

    public function run(): void
    {
        foreach (self::ARTISTS as $artist) {
            Artist::query()->firstOrCreate(
                ['slug' => Str::slug($artist['name'])],
                [
                    'name' => $artist['name'],
                    'bio' => $artist['bio'],
                    'country' => $artist['country'],
                    'website' => $artist['website'],
                    'is_active' => true,
                ],
            );
        }
    }
}
