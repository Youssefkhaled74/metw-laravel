<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use App\Models\State;
use Illuminate\Database\Seeder;

class CityGovernorateBackfillSeeder extends Seeder
{
    public function run(): void
    {
        City::withoutGlobalScopes()
            ->whereNull('governorate_id')
            ->whereNotNull('state_id')
            ->chunkById(200, function ($cities) {
                foreach ($cities as $city) {
                    $state = State::withoutGlobalScopes()->find($city->state_id);

                    if (! $state) {
                        continue;
                    }

                    $governorate = Governorate::withoutGlobalScopes()
                        ->where('name_ar', $state->name_ar)
                        ->first();

                    if (! $governorate) {
                        continue;
                    }

                    $city->update(['governorate_id' => $governorate->id]);
                }
            });
    }
}
