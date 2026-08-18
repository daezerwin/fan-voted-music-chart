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
        [
            'name' => 'Taylor Swift',
            'country' => 'US',
            'bio' => 'American singer-songwriter whose catalog spans country and pop, from Fearless to Lover.',
            'website' => null,
        ],
        [
            'name' => 'The Red Jumpsuit Apparatus',
            'country' => 'US',
            'bio' => 'American rock band best known for the single "Face Down."',
            'website' => null,
        ],
        [
            'name' => 'David Cook',
            'country' => 'US',
            'bio' => 'American rock singer and American Idol season 7 winner.',
            'website' => null,
        ],
        [
            'name' => 'Daughtry',
            'country' => 'US',
            'bio' => 'American rock band fronted by Chris Daughtry, known for "It\'s Not Over" and "Home."',
            'website' => null,
        ],
        [
            'name' => 'The Script',
            'country' => 'IE',
            'bio' => 'Irish pop rock band known for "Breakeven" and "Hall of Fame."',
            'website' => null,
        ],
        [
            'name' => 'Whitney Houston',
            'country' => 'US',
            'bio' => 'American singer and one of the best-selling music artists of all time.',
            'website' => null,
        ],
        [
            'name' => 'Celine Dion',
            'country' => 'CA',
            'bio' => 'Canadian singer known for the ballad "My Heart Will Go On."',
            'website' => null,
        ],
        [
            'name' => 'Mariah Carey',
            'country' => 'US',
            'bio' => 'American singer known for her vocal range and multi-octave hits.',
            'website' => null,
        ],
        [
            'name' => 'Boyz II Men',
            'country' => 'US',
            'bio' => 'American R&B vocal group known for ballads like "End of the Road."',
            'website' => null,
        ],
        [
            'name' => 'Toni Braxton',
            'country' => 'US',
            'bio' => 'American R&B singer known for "Un-Break My Heart."',
            'website' => null,
        ],
        [
            'name' => 'Bryan Adams',
            'country' => 'CA',
            'bio' => 'Canadian rock singer-songwriter known for "(Everything I Do) I Do It for You."',
            'website' => null,
        ],
        [
            'name' => 'Extreme',
            'country' => 'US',
            'bio' => 'American rock band best known for the ballad "More Than Words."',
            'website' => null,
        ],
        [
            'name' => 'Roxette',
            'country' => 'SE',
            'bio' => 'Swedish pop rock duo known for "It Must Have Been Love."',
            'website' => null,
        ],
        [
            'name' => 'Berlin',
            'country' => 'US',
            'bio' => 'American new wave band known for "Take My Breath Away."',
            'website' => null,
        ],
        [
            'name' => 'Foreigner',
            'country' => 'US',
            'bio' => 'British-American rock band known for "I Want to Know What Love Is."',
            'website' => null,
        ],
        [
            'name' => 'Richard Marx',
            'country' => 'US',
            'bio' => 'American singer-songwriter known for the ballad "Right Here Waiting."',
            'website' => null,
        ],
        [
            'name' => 'Bonnie Tyler',
            'country' => 'GB',
            'bio' => 'Welsh singer known for "Total Eclipse of the Heart."',
            'website' => null,
        ],
        [
            'name' => 'Sade',
            'country' => 'GB',
            'bio' => 'British band fronted by singer Sade Adu, known for smooth soul hits like "No Ordinary Love."',
            'website' => null,
        ],
        [
            'name' => 'Shania Twain',
            'country' => 'CA',
            'bio' => 'Canadian singer known for blending country and pop, including "You\'re Still the One."',
            'website' => null,
        ],
        [
            'name' => 'Vanessa Williams',
            'country' => 'US',
            'bio' => 'American singer and actress known for "Save the Best for Last."',
            'website' => null,
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
