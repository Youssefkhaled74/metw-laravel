<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\State;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GovernoratesAndCitiesExcelSeeder extends Seeder
{
    private const STATE_NAMES_EN = [
        'القاهرة' => 'Cairo',
        'الإسكندرية' => 'Alexandria',
        'الجيزة' => 'Giza',
        'القليوبية' => 'Qalyubia',
        'الغربية' => 'Gharbia',
        'الشرقية' => 'Sharqia',
        'الدقهلية' => 'Dakahlia',
        'كفر الشيخ' => 'Kafr El Sheikh',
        'البحيرة' => 'Beheira',
        'دمياط' => 'Damietta',
        'المنوفية' => 'Monufia',
        'مطروح' => 'Matrouh',
        'بور سعيد' => 'Port Said',
        'الإسماعيلية' => 'Ismailia',
        'السويس' => 'Suez',
        'جنوب سيناء' => 'South Sinai',
        'شمال سيناء' => 'North Sinai',
        'البحر الأحمر' => 'Red Sea',
        'الفيوم' => 'Faiyum',
        'بني سويف' => 'Beni Suef',
        'المنيا' => 'Minya',
        'أسيوط' => 'Assiut',
        'سوهاج' => 'Sohag',
        'قنا' => 'Qena',
        'الأقصر' => 'Luxor',
        'أسوان' => 'Aswan',
        'الوادي الجديد' => 'New Valley',
    ];

    public function run(): void
    {
        $path = database_path('sql/Governorates & Cities.xlsx');

        if (!file_exists($path)) {
            $this->command?->warn("Governorates Excel file not found: {$path}");
            return;
        }

        $country = Country::firstOrCreate(
            ['name_en' => 'Egypt'],
            ['name_ar' => 'مصر', 'is_active' => 1]
        );

        $rows = IOFactory::load($path)->getActiveSheet()->toArray();
        $capitalCities = [];

        foreach (array_slice($rows, 1) as $row) {
            $excelSort = (int) ($row[0] ?? 0);
            $governorateNumber = (int) ($row[1] ?? 0);
            $governorateName = $this->clean($row[2] ?? null);
            $capitalMarker = $this->clean($row[3] ?? null);
            $cityName = $this->clean($row[4] ?? null);

            if (!$excelSort || !$governorateNumber || !$governorateName || !$cityName) {
                continue;
            }

            $governorate = Governorate::withoutGlobalScopes()->updateOrCreate(
                ['governorate_number' => $governorateNumber],
                [
                    'name_ar' => $governorateName,
                    'is_active' => true,
                ]
            );
            $governorate->restore();

            $state = $this->stateForGovernorate($country, $governorateName);
            $isCapital = $capitalMarker === 'عاصمة';

            $city = City::withoutGlobalScopes()->updateOrCreate(
                [
                    'governorate_id' => $governorate->id,
                    'name_ar' => $cityName,
                ],
                [
                    'name_en' => $cityName,
                    'state_id' => $state?->id,
                    'excel_sort' => $excelSort,
                    'is_capital' => $isCapital,
                    'is_active' => true,
                ]
            );
            $city->restore();

            if ($isCapital) {
                $capitalCities[$governorate->id] = $city->id;
            }
        }

        foreach ($capitalCities as $governorateId => $cityId) {
            Governorate::withoutGlobalScopes()
                ->whereKey($governorateId)
                ->update(['capital_city_id' => $cityId]);
        }
    }

    private function stateForGovernorate(Country $country, string $governorateName): ?State
    {
        $nameEn = self::STATE_NAMES_EN[$governorateName] ?? null;

        if (!$nameEn) {
            return null;
        }

        $state = State::withoutGlobalScopes()->updateOrCreate(
            [
                'name_en' => $nameEn,
                'country_id' => $country->id,
            ],
            [
                'name_ar' => $governorateName,
                'is_active' => true,
            ]
        );
        $state->restore();

        return $state;
    }

    private function clean(?string $value): ?string
    {
        $value = preg_replace('/\s+/u', ' ', str_replace("\xc2\xa0", ' ', (string) $value));
        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
