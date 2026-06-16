<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\State;
use App\Models\City;

class ShipmentLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ar' ? 'name_ar' : 'name_en';

        $allStateIds = collect($this->resource)
            ->pluck('state')
            ->flatten()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $allCityIds = collect($this->resource)
            ->pluck('city')
            ->flatten()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $states = State::whereIn('id', $allStateIds)
            ->select('id', $nameColumn)
            ->get()
            ->keyBy('id');

        $cities = City::whereIn('id', $allCityIds)
            ->select('id', $nameColumn)
            ->get()
            ->keyBy('id');

        $coverage = [];

        foreach ($states as $stateId => $state) {
            $stateName = $state->$nameColumn;

            $stateCities = $cities->map(function ($city) use ($nameColumn) {
                return [
                    'id'   => $city->id,
                    'name' => $city->$nameColumn,
                ];
            })->values()->toArray();

            // ✅ KEY BY STATE NAME
            $coverage[$stateName] = [
                'state_id'   => $state->id,
                'state_name' => $stateName,
                'areas'     => $stateCities
            ];
        }

        return $coverage;
    }
}
