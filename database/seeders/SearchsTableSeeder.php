<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Search;

class SearchSeeder extends Seeder
{
    public function run()
    {
        Search::factory(10)->create();
    }
}
