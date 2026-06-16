<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\City;

class ZonesTableSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['city' => 'Heliopolis', 'name_en' => 'El Nozha', 'name_ar' => 'النزهة'],
            ['city' => 'Heliopolis', 'name_en' => 'Korba', 'name_ar' => 'الكوربة'],
            ['city' => 'Nasr City', 'name_en' => 'El Hay El Sabea', 'name_ar' => 'الحي السابع'],
            ['city' => 'Nasr City', 'name_en' => 'El Hay El Asher', 'name_ar' => 'الحي العاشر'],
            ['city' => 'Dokki', 'name_en' => 'Tahrir Street', 'name_ar' => 'شارع التحرير'],
            ['city' => 'Mohandessin', 'name_en' => 'Lebanon Square', 'name_ar' => 'ميدان لبنان'],
            ['city' => 'Sidi Gaber', 'name_en' => 'Mostafa Kamel', 'name_ar' => 'مصطفى كامل'],
            ['city' => 'Smouha', 'name_en' => 'El Nasr St', 'name_ar' => 'شارع النصر'],
            ['city' => 'Mansoura', 'name_en' => 'Talkha', 'name_ar' => 'طلخا'],
            ['city' => 'Zagazig', 'name_en' => 'El Qawmia', 'name_ar' => 'القومية'],
        ];

        foreach ($zones as $zone) {
            $city = City::where('name_en', $zone['city'])->first();
            if (!$city) continue;

            Zone::updateOrCreate(
                ['name_en' => $zone['name_en'], 'city_id' => $city->id],
                ['name_ar' => $zone['name_ar'], 'is_active' => 1, 'city_id' => $city->id]
            );
        }
    }
}
