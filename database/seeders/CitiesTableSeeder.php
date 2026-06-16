<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\State;

class CitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['state' => 'Cairo', 'name_en' => 'Heliopolis', 'name_ar' => 'مصر الجديدة'],
            ['state' => 'Cairo', 'name_en' => 'Nasr City', 'name_ar' => 'مدينة نصر'],
            ['state' => 'Giza', 'name_en' => 'Dokki', 'name_ar' => 'الدقي'],
            ['state' => 'Giza', 'name_en' => 'Mohandessin', 'name_ar' => 'المهندسين'],
            ['state' => 'Alexandria', 'name_en' => 'Sidi Gaber', 'name_ar' => 'سيدي جابر'],
            ['state' => 'Alexandria', 'name_en' => 'Smouha', 'name_ar' => 'سموحة'],
            ['state' => 'Dakahlia', 'name_en' => 'Mansoura', 'name_ar' => 'المنصورة'],
            ['state' => 'Sharqia', 'name_en' => 'Zagazig', 'name_ar' => 'الزقازيق'],
            ['state' => 'Fayoum', 'name_en' => 'Ibshway', 'name_ar' => 'إبشواي'],
            ['state' => 'Aswan', 'name_en' => 'Edfu', 'name_ar' => 'إدفو'],
        ];

        foreach ($cities as $city) {
            $state = State::where('name_en', $city['state'])->first();
            if (!$state) continue;

            City::updateOrCreate(
                ['name_en' => $city['name_en'], 'state_id' => $state->id],
                ['name_ar' => $city['name_ar'], 'is_active' => 1, 'state_id' => $state->id]
            );
        }
    }
}
