<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Country;

class StatesSeeder extends Seeder
{
    public function run()
    {
        $country = Country::withoutGlobalScopes()
            ->where(function ($query) {
                $query->where('name_en', 'Egypt')
                    ->orWhere('name_ar', 'مصر')
                    ->orWhere('phone_code', '+20');
            })
            ->first();

        if (! $country) {
            $country = Country::withoutGlobalScopes()->create([
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
                'is_active' => true,
                'phone_code' => '+20',
            ]);
        } else {
            $country->update([
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
                'is_active' => true,
                'phone_code' => '+20',
            ]);
        }

        $country->restore();

        $states = [
            ['name_en' => 'Cairo', 'name_ar' => 'القاهرة'],
            ['name_en' => 'Giza', 'name_ar' => 'الجيزة'],
            ['name_en' => 'Alexandria', 'name_ar' => 'الإسكندرية'],
            ['name_en' => 'Port Said', 'name_ar' => 'بورسعيد'],
            ['name_en' => 'Suez', 'name_ar' => 'السويس'],
            ['name_en' => 'Dakahlia', 'name_ar' => 'الدقهلية'],
            ['name_en' => 'Sharqia', 'name_ar' => 'الشرقية'],
            ['name_en' => 'Qalyubia', 'name_ar' => 'القليوبية'],
            ['name_en' => 'Kafr El Sheikh', 'name_ar' => 'كفر الشيخ'],
            ['name_en' => 'Gharbia', 'name_ar' => 'الغربية'],
            ['name_en' => 'Monufia', 'name_ar' => 'المنوفية'],
            ['name_en' => 'Beheira', 'name_ar' => 'البحيرة'],
            ['name_en' => 'Ismailia', 'name_ar' => 'الإسماعيلية'],
            ['name_en' => 'Damietta', 'name_ar' => 'دمياط'],
            ['name_en' => 'Faiyum', 'name_ar' => 'الفيوم'],
            ['name_en' => 'Beni Suef', 'name_ar' => 'بني سويف'],
            ['name_en' => 'Minya', 'name_ar' => 'المنيا'],
            ['name_en' => 'Assiut', 'name_ar' => 'أسيوط'],
            ['name_en' => 'Sohag', 'name_ar' => 'سوهاج'],
            ['name_en' => 'Qena', 'name_ar' => 'قنا'],
            ['name_en' => 'Luxor', 'name_ar' => 'الأقصر'],
            ['name_en' => 'Aswan', 'name_ar' => 'أسوان'],
            ['name_en' => 'Red Sea', 'name_ar' => 'البحر الأحمر'],
            ['name_en' => 'New Valley', 'name_ar' => 'الوادي الجديد'],
            ['name_en' => 'Matrouh', 'name_ar' => 'مرسى مطروح'],
            ['name_en' => 'North Sinai', 'name_ar' => 'شمال سيناء'],
            ['name_en' => 'South Sinai', 'name_ar' => 'جنوب سيناء'],
        ];

        foreach ($states as $stateData) {
            $state = State::withoutGlobalScopes()->updateOrCreate(
                [
                    'name_en' => $stateData['name_en'],
                ],
                [
                    'name_ar' => $stateData['name_ar'],
                    'country_id' => $country->id,
                    'is_active' => true,
                ]
            );
            $state->restore();
        }
    }
}
