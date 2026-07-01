<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GovernoratesAndCitiesExcelSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sql/Governorates & Cities.xlsx');

        if (! file_exists($path)) {
            $this->command?->warn("Governorates Excel file not found: {$path}");

            return;
        }

        $rows = IOFactory::load($path)->getActiveSheet()->toArray();
        $capitalCities = [];

        foreach (array_slice($rows, 1) as $row) {
            $excelSort = (int) ($row[0] ?? 0);
            $governorateNumber = (int) ($row[1] ?? 0);
            $governorateNameAr = $this->clean($row[2] ?? null);
            $capitalMarker = $this->clean($row[3] ?? null);
            $cityNameAr = $this->clean($row[4] ?? null);

            if (! $excelSort || ! $governorateNumber || ! $governorateNameAr || ! $cityNameAr) {
                continue;
            }

            $governorate = Governorate::withoutGlobalScopes()->updateOrCreate(
                ['governorate_number' => $governorateNumber],
                [
                    'name_ar' => $governorateNameAr,
                    'is_active' => true,
                ]
            );
            $governorate->restore();

            $isCapital = Str::contains((string) $capitalMarker, ['عاصمة', 'capital']);

            $city = City::withoutGlobalScopes()->updateOrCreate(
                [
                    'governorate_id' => $governorate->id,
                    'name_ar' => $cityNameAr,
                ],
                [
                    'name_en' => $this->cityNameEn($cityNameAr),
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

    private function clean(?string $value): ?string
    {
        $value = preg_replace('/\s+/u', ' ', str_replace("\xc2\xa0", ' ', (string) $value));
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function cityNameEn(string $cityNameAr): string
    {
        return $cityNameAr;
    }
}
