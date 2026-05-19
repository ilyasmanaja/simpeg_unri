<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\JabatanFungsionalSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JabatanFungsionalSeeder::class,
        ]);
    }
}