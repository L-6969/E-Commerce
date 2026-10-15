<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin accounts must be created with an individually chosen password.
        // Do not seed a shared, predictable login into any environment.
    }
}
