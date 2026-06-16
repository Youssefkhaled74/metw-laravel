<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Country
        $egypt = Location::firstOrCreate(
            ['name' => 'Egypt', 'type' => 'country'],
            ['parent_id' => null, 'path' => 'EG']
        );

        // Governorates
        $cairo = Location::firstOrCreate(
            ['name' => 'Cairo', 'type' => 'state', 'parent_id' => $egypt->id],
            ['path' => 'EG/Cairo']
        );

        $alex = Location::firstOrCreate(
            ['name' => 'Alexandria', 'type' => 'state', 'parent_id' => $egypt->id],
            ['path' => 'EG/Alexandria']
        );

        $giza = Location::firstOrCreate(
            ['name' => 'Giza', 'type' => 'state', 'parent_id' => $egypt->id],
            ['path' => 'EG/Giza']
        );

        // Cairo cities
        $nasrCity = Location::firstOrCreate(
            ['name' => 'Nasr City', 'type' => 'city', 'parent_id' => $cairo->id],
            ['path' => 'EG/Cairo/Nasr City']
        );

        $heliopolis = Location::firstOrCreate(
            ['name' => 'Masr El Gedida', 'type' => 'city', 'parent_id' => $cairo->id],
            ['path' => 'EG/Cairo/Masr El Gedida']
        );

        // Alexandria cities
        $smouha = Location::firstOrCreate(
            ['name' => 'Smouha', 'type' => 'city', 'parent_id' => $alex->id],
            ['path' => 'EG/Alexandria/Smouha']
        );

        $miami = Location::firstOrCreate(
            ['name' => 'Miami', 'type' => 'city', 'parent_id' => $alex->id],
            ['path' => 'EG/Alexandria/Miami']
        );

        // Giza cities
        $dokki = Location::firstOrCreate(
            ['name' => 'Dokki', 'type' => 'city', 'parent_id' => $giza->id],
            ['path' => 'EG/Giza/Dokki']
        );

        $mohandessin = Location::firstOrCreate(
            ['name' => 'Mohandessin', 'type' => 'city', 'parent_id' => $giza->id],
            ['path' => 'EG/Giza/Mohandessin']
        );
    }
}
