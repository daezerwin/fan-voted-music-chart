<?php

namespace Database\Seeders;

use App\Models\Artist;
use Illuminate\Database\Seeder;

class ArtistSeeder extends Seeder
{
    public function run(): void
    {
        Artist::factory()->count(15)->create();

        Artist::factory()->inactive()->count(2)->create();
    }
}
