<?php

namespace Tests\Feature\Models;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_song_belongs_to_its_artist_and_genre(): void
    {
        $artist = Artist::factory()->create();
        $genre = Genre::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'genre_id' => $genre->id,
        ]);

        $this->assertTrue($song->artist->is($artist));
        $this->assertTrue($song->genre->is($genre));
        $this->assertTrue($artist->songs->contains($song));
        $this->assertTrue($genre->songs->contains($song));
    }

    public function test_song_factory_defaults_are_active_and_votable(): void
    {
        $song = Song::factory()->create();

        $this->assertTrue($song->is_active);
        $this->assertTrue($song->voting_enabled);
    }
}
