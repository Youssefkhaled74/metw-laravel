<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MetwGoLoginSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TransportTypeSeeder::class,
            RepresentativeWorkTypeOptionsSeeder::class,
            WarehouseSeeder::class,
            MetwGoTestSeeder::class,
        ]);

        $this->command?->info('MetwGo login seeder completed.');
        $this->command?->info('Login phone: 01000000000');
        $this->command?->info('Login password: 12345678');
    }
}
